<?php

namespace App\Services;

use App\Actions\DatabaseAction;
use App\Constants\WhatsAppConstants;
use App\Dialog360\DataTransferObjects\Dialog360WebhookDTO;
use App\Dialog360\Dialog360;
use App\Enums\SchoolSosAleart;
use App\Models\Annoucement;
use App\Models\BusinessChat;
use App\Models\Restaurant;
use App\Models\RestaurantCart;
use App\Models\RestaurantMenuItem;
use App\Models\RestaurantOffer;
use App\Models\School;
use App\Models\SchoolEvent;
use App\Models\SchoolSosAlert;
use App\Models\User;
use App\Models\RestaurantMember;
use App\Models\SchoolMember;
use App\Models\BusinessMember;
use App\OpenAI\OpenAI;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class MessageHandlers
{
  private Dialog360 $dialog360;
  private OpenAI $openAI;
  private DatabaseAction $databaseAction;



  public function __construct(Dialog360 $dialog360, OpenAI $openAI, DatabaseAction $databaseAction)
  {
    $this->dialog360 = $dialog360;
    $this->openAI = $openAI;
    $this->databaseAction = $databaseAction;
  }

  public function handleGreeting(User $user, string $text, string $ln = 'en'): void
  {
    $ln = $ln === 'cs' ? WhatsAppConstants::LANGUAGE_CS : WhatsAppConstants::LANGUAGE_EN;
    $role = $user->role->slug;

    if (in_array($role, ['school', 'school_member'])) {
      $responseText = $ln === WhatsAppConstants::LANGUAGE_CS
        ? config('constant.whatsapp_templates.hi_school.body.text_cs')
        : config('constant.whatsapp_templates.hi_school.body.text');
      $this->databaseAction->storeConversation($user, $text, $responseText);
      $ln === WhatsAppConstants::LANGUAGE_CS
        ? $this->dialog360->sendSchoolModuleButtonsCs($user)
        : $this->dialog360->sendSchoolModuleButtons($user);
      return;
    }

    if (in_array($role, ['restaurant', 'restaurant_member'])) {
      $responseText = $ln === WhatsAppConstants::LANGUAGE_CS
        ? config('constant.whatsapp_templates.hi_restaurant.body.text_cs')
        : config('constant.whatsapp_templates.hi_restaurant.body.text');
      $this->databaseAction->storeConversation($user, $text, $responseText);
      $ln === WhatsAppConstants::LANGUAGE_CS
        ? $this->dialog360->sendRestaurantModuleButtonsCs($user)
        : $this->dialog360->sendRestaurantModuleButtons($user);
      return;
    }

    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      WhatsAppConstants::TEMPLATE_NEW_USER,
      $ln
    );
    $renderedText = $this->dialog360->renderTemplate(WhatsAppConstants::TEMPLATE_NEW_USER, []);
    $this->databaseAction->storeConversation($user, $text, $renderedText);
  }

  public function handleRestaurantDetails(User $user, $restaurantId = null): void
  {

    $restaurant = Restaurant::with(['cuisines', 'categories', 'timings'])
        ->find($restaurantId);

    if ($restaurant) {
      $this->prepareRestaurantData($restaurant, $user->language_code);
      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendRestaurantWhatsAppTemplateMessageCs($user, $restaurant) :
        $this->dialog360->sendRestaurantWhatsAppTemplateMessage($user, $restaurant);
    } else {
      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        WhatsAppConstants::RESPONSE_RESTAURANT_NOT_FOUND
      );
    }
  }

  public function handleAudioMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    try {
      $audioId = $webhookDTO->audioId;
      if (!$audioId) {
        return;
      }

      $audioData = $this->dialog360->getWhatsAppMedia($audioId);
      if (!$audioData || !isset($audioData['url'])) {
        return;
      }

      $mediaData = $this->parseMediaUrl($audioData['url']);
      $audioFile = $this->dialog360->downloadWhatsAppMedia($mediaData['relative_url']);
      $filePath = $this->saveAudioFile($mediaData['mid'], $audioFile);

      $text = $this->openAI->generateOpenAIWhisperResponse($mediaData['mid'] . WhatsAppConstants::AUDIO_FILE_EXTENSION, $filePath);

      if ((new MessageValidator($this->dialog360))->isGreeting($text)) {
        $this->handleGreeting($user, $text, $webhookDTO->languageCode);
      } else {
        if(in_array($user->role->slug, ['school','school_member','restaurant', 'restaurant_member'])) {
          $this->processBusinessMessage($user, $text);
        }
        else
        {
          $this->processRegularMessage($user, $text);
        }
      }

      $this->cleanupAudioFile($filePath);
    } catch (QueryException $e) {
      info('Failed to process audio message', ['error' => $e->getMessage()]);
    }
  }

  public function handleLocationMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    try {
      $user->update([
        'latitude' => $webhookDTO->location['latitude'] ?? null,
        'longitude' => $webhookDTO->location['longitude'] ?? null,
        'address' => $webhookDTO->location['address'] ?? null
      ]);

      $user->refresh();
      $userMessage = 'user information : ' . json_encode([
          'name' => $user->name,
          'address' => $user->address,
          'message' => "Send details ask in previous message.",
        ]);

      $responseDTO = $this->openAI->generateOpenAICompletionResponse($user, $userMessage);
      $this->dialog360->sendWhatsAppMessage($user->mobile_number, $responseDTO->getContent());
    } catch (QueryException $e) {
      info('Failed to update user location', ['error' => $e->getMessage()]);
    }
  }

  public function handleInteractiveMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    try {
      if ($webhookDTO->interactiveType === WhatsAppConstants::INTERACTIVE_TYPE_BUTTON_REPLY) {
        $this->handleButtonReply($user, $webhookDTO->buttonReply);
      } elseif ($webhookDTO->interactiveType === WhatsAppConstants::INTERACTIVE_TYPE_LIST_REPLY) {
        $this->handleListReply($user, $webhookDTO->buttonReply);
      }
    } catch (QueryException $e) {
      info('Failed to process interactive message', ['error' => $e->getMessage()]);
    }
  }

  public function processButtonText(string $text): string
  {
    return WhatsAppConstants::BUTTON_TEXT_MAPPINGS[$text] ?? WhatsAppConstants::DEFAULT_BUTTON_TEXT;
  }

  public function processRegularMessage(User $user, string $text): void
  {
    $response = $this->getLangChainResponse($user, $text);
    info('LangChain response for processRegularMessage', ['response' => $response]);
    info('LangChain Response for the given text '. $text);
    $replyMessage = $response['reply'];

    // // ✅ If reply contains a job-application link, append ?lang={detected_language}
    // if (!empty($response['detected_language'])) {
    //     $detectedLang = $response['detected_language'];
    //     $replyMessage = preg_replace_callback(
    //         '/https:\/\/service\.citio\.cool\/job-application\/[a-z0-9\-]+/i',
    //         function ($matches) use ($detectedLang) {
    //             return $matches[0] . '?lang=' . urlencode($detectedLang);
    //         },
    //         $replyMessage
    //     );
    // }

    // ✅ If reply contains any form link, append ?lang={detected_language}
    if (!empty($response['detected_language'])) {
        $detectedLang = $response['detected_language'];

        // Match ANY Citio form link, not just job-application
        $replyMessage = preg_replace_callback(
            '/https:\/\/service\.citio\.cool\/(?:[a-z0-9\-]+\/)?[a-z0-9\-]+/i',
            function ($matches) use ($detectedLang) {
                $url = $matches[0];

                // If the URL already has query params, append with &
                if (strpos($url, '?') !== false) {
                    return $url . '&lang=' . urlencode($detectedLang);
                }

                return $url . '?lang=' . urlencode($detectedLang);
            },
            $replyMessage
        );
    }

    if (!empty($response['captured_entity'])) {
        $this->linkUserToBusinessFromContext($user,$response['captured_entity']);
        if ($response['captured_entity']['type'] == 'restaurant') {
           $this->handleRestaurantCapturedEntity($user, $response['captured_entity']);
           return;
        }
    }
    // Send response and store conversation
    $this->dialog360->sendWhatsAppMessage($user->mobile_number, $replyMessage);
    $this->databaseAction->storeConversation($user, $text, $replyMessage);

  }

  public function handleBusinessInteractiveMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    try {
      if ($webhookDTO->interactiveType === WhatsAppConstants::INTERACTIVE_TYPE_LIST_REPLY || $webhookDTO->interactiveType === WhatsAppConstants::INTERACTIVE_TYPE_BUTTON_REPLY) {
        $this->handleBusinessReply($user, $webhookDTO->buttonReply);
      }
    } catch (QueryException $e) {
      info('Failed to process interactive message', ['error' => $e->getMessage()]);
    }
  }
  private function handleBusinessReply(User $user, array $replyText): void
  {
    if ($user->role->slug == WhatsAppConstants::TYPE_SCHOOL || $user->role->slug == WhatsAppConstants::TYPE_SCHOOL_MEMBER) {
      $this->handleSchoolBusinessReply($user, $replyText);
      return;
    }
    elseif ($user->role->slug == WhatsAppConstants::TYPE_RESTAURANT || $user->role->slug == WhatsAppConstants::TYPE_RESTAURANT_MEMBER) {
      $this->handleRestaurantBusinessReply($user, $replyText);
      return;
    }
    info('Unhandled business reply type for user role: ' . $user->role->slug . ' with action: ' . WhatsAppConstants::TYPE_SCHOOL);

  }
  public function handleRestaurantBusinessReply(User $user, array $replyText): void
  {
    match ($replyText['action']) {

      // Announcement
      WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT => $this->sendBusinessAddPrototype($user,WhatsAppConstants::TEMPLATE_RESTAURANT_NEW_ANNOUNCEMENT_PROTOTYPE,WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT),
      WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CONFIRM => $this->addRestaurantBusinessRecord($user,$replyText),
      WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CANCEL =>  $this->cancelBussinessChat($user,$replyText),
      WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT =>  $this->editRestaurantBusinessRecord($user,$replyText),

      // Offer
      WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER => $this->handleRestaurantOfferLogic($user),
      WhatsAppConstants::RESTAURANT_OFFER_ADD_CONFIRM => $this->addRestaurantBusinessRecord($user,$replyText),
      WhatsAppConstants::RESTAURANT_OFFER_ADD_CANCEL =>  $this->cancelBussinessChat($user,$replyText),
      WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT =>  $this->editRestaurantBusinessRecord($user,$replyText),

      default => null
    };
  }
  public function handleRestaurantOfferLogic(User $user)
  {
      $businessMember = BusinessMember::where('user_id', $user->id)->first();
      $restaurant = null;

      if ($businessMember && $businessMember->businessable_type === Restaurant::class) {
          $restaurant = Restaurant::with(['items', 'offers.applicableItems'])->find($businessMember->businessable_id);

          if ($restaurant) {
              $totalMenuItems = $restaurant->items->count();
              // $totalItemsInOffers = $restaurant->offers
              //                       ->flatMap(function ($offer) {
              //                           return $offer->applicableItems;
              //                       })->unique('id')->count();

              $totalItemsInOffers = DB::table('offer_menu_item')
                              ->join('restaurant_offers', 'restaurant_offers.id', '=', 'offer_menu_item.offer_id')
                              ->where('restaurant_offers.restaurant_id', '=', $restaurant->id)
                              // ->where('restaurant_offers.starts_from', '<=', now())
                              // ->where('restaurant_offers.ends_at', '>=', now())
                              ->count();


              info('Total menu items: ' . $totalMenuItems);
              info('Total items in offers: ' . $totalItemsInOffers);

              // ❌ If menu item count condition is incorrect, this block will always be true
              if ($totalMenuItems === $totalItemsInOffers) {
                  BusinessChat::where('user_id', $user->id)
                      ->latest()
                      ->first()
                      ->update(['is_completed' => true]);
                  $confirmMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS
                      ? "Nemůžete přidat další nabídky. Dosáhli jste maximálního počtu položek v nabídce. Nejprve musíte přidat další položky v nabídce."
                      : "You cant add more offers. You have reached the maximum limit of menu items. First, you have to add more menu items.";

                  $this->dialog360->sendWhatsAppMessage(
                      $user->mobile_number,
                      $confirmMessage
                  );
                  return; // ❌ Stop execution here
              } elseif ($totalMenuItems == 0) {
                  BusinessChat::where('user_id', $user->id)
                      ->latest()
                      ->first()
                      ->update(['is_completed' => true]);
                  $noMenuItemMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS
                      ? "Než přidáte nabídky, musíte nejprve přidat položky do nabídky."
                      : "You must first add menu items before adding offers.";

                  $this->dialog360->sendWhatsAppMessage(
                      $user->mobile_number,
                      $noMenuItemMessage
                  );
                  return; // ❌ Stop execution here
              } else {
                // Debug logs
                  info('Restaurant is for offer logic ' . json_encode($restaurant));
                  info('Total menu items count: ' . ($totalMenuItems ?? 0));
                  info('Total count in items is : ' . ($restaurant ? $restaurant->items()->count() : 0));

                  // ✅ Otherwise continue with normal flow
                  return $this->sendBusinessAddPrototype(
                      $user,
                      WhatsAppConstants::TEMPLATE_RESTAURANT_NEW_OFFER_PROTOTYPE,
                      WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER
                  );
              }
          } else {
              info('Restaurant not found for business member with ID: ' . $businessMember->businessable_id);
              return;
          }
      }
  }
  public function handleSchoolBusinessReply(User $user, array $replyText): void
  {
    info('Handling school business reply with action: ' . $replyText['action']);
    match ($replyText['action']) {
      // SOS Alert
      WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT => $this->sendBusinessAddPrototype($user,WhatsAppConstants::TEMPLATE_SCHOOL_NEW_SOS_ALERT_PROTOTYPE,WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT),
      WhatsAppConstants::SCHOOL_SOS_ADD_CONFIRM => $this->addSchoolBusinessRecord($user,$replyText),
      WhatsAppConstants::SCHOOL_SOS_ADD_CANCEL =>  $this->cancelBussinessChat($user,$replyText),
      WhatsAppConstants::SCHOOL_SOS_ADD_EDIT =>  $this->editSchoolBusinessRecord($user,$replyText),

      // Announcement
      WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT => $this->sendBusinessAddPrototype($user,WhatsAppConstants::TEMPLATE_SCHOOL_NEW_ANNOUNCEMENT_PROTOTYPE,WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT),
      WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CONFIRM => $this->addSchoolBusinessRecord($user,$replyText),
      WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CANCEL =>  $this->cancelBussinessChat($user,$replyText),
      WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT =>  $this->editSchoolBusinessRecord($user,$replyText),

      // Event
      WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT => $this->sendBusinessAddPrototype($user,WhatsAppConstants::TEMPLATE_SCHOOL_NEW_EVENT_PROTOTYPE,WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT),
      WhatsAppConstants::SCHOOL_EVENT_ADD_CONFIRM => $this->addSchoolBusinessRecord($user,$replyText),
      WhatsAppConstants::SCHOOL_EVENT_ADD_CANCEL =>  $this->cancelBussinessChat($user,$replyText),
      WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT =>  $this->editSchoolBusinessRecord($user,$replyText),
      default => null
    };
  }
  public function processBusinessMessage(User $user, string $text): void
  {
      $businessChat = BusinessChat::where('user_id', $user->id)
        ->latest()
        ->first();
      info('businesschat type is '. $businessChat->message_type);
      $is_edit = match($businessChat->message_type) {
        WhatsAppConstants::SCHOOL_SOS_ADD_EDIT => true,
        WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT => true,
        WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT => true,
        WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT => true,
        WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT => true,
        default => false
      };
      info('process Business Message: ' . $text);
      if( $businessChat->is_completed == false) {
        info('Continuing existing business chat with ID: ' . $businessChat->id);
        info('is_edit is ' . ($is_edit ? 'true' : 'false'));

        // if($is_edit == false && str_starts_with($businessChat->message_type, 'add')){
        //   info('New business chat cannot be started until the current one is completed.');

        //   $uncompleteMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        //        'Máte probíhající obchodní operaci. Před zahájením nové ji prosím dokončete nebo zrušte.' :
        //        'You have an ongoing business operation. Please complete or cancel it before starting a new one.';

        //   $this->dialog360->sendWhatsAppMessage($user->mobile_number, $uncompleteMessage);
        //   $this->databaseAction->storeConversation($user, $text, $uncompleteMessage);
        //   return;
        // }

        $response = $this->getLangChainBusinessResponse($user, $text, $businessChat, $is_edit);

        $replyMessage = $response['reply'];
        info('get business messages reply: ' . $replyMessage);
        if($replyMessage != 'Message processed'){
          $this->dialog360->sendWhatsAppMessage($user->mobile_number, $replyMessage);
          return;
        }
        // Send response and store conversation

        if ($response['extracted_data']) {
          $data = $response['extracted_data'] ?? [];
          if ($businessChat) {
            $businessChat->update([
              'data' => json_encode($response)
            ]);
          } else {
            $businessChat = BusinessChat::create([
              'user_id' => $user->id,
              'data' => json_encode($response)
            ]);
          }
          $data['chat_id'] = $businessChat->id;
          $isSchool = in_array($user->role->slug, ['school', 'school_member']);
          $isRestaurant = in_array($user->role->slug, ['restaurant', 'restaurant_member']);
          $langCs = $user->language_code == WhatsAppConstants::LANGUAGE_CS;

          $this->databaseAction->storeConversation($user, $text, json_encode($data));

          info('Process business Message method and the businessChat->message_type is : ' . $businessChat->message_type);
          info('isSchool is ' . ($isSchool ? 'true' : 'false'));
          info('isRestaurant is ' . ($isRestaurant ? 'true' : 'false'));
          info('langCs is ' . ($langCs ? 'true' : 'false'));

          if ($isSchool) {
            switch ($businessChat->message_type) {
              case WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT:
                $langCs
                      ? $this->dialog360->sendSchoolSosConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolSosConfirmationTemplate($user, $data);
                  break;
              case WhatsAppConstants::SCHOOL_SOS_ADD_EDIT:
                  $langCs
                      ? $this->dialog360->sendSchoolSosConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolSosConfirmationTemplate($user, $data);
                  break;
              case WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT:
                $langCs
                      ? $this->dialog360->sendSchoolAnnouncementConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolAnnouncementConfirmationTemplate($user, $data);
                  break;
              case WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT:
                  $langCs
                      ? $this->dialog360->sendSchoolAnnouncementConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolAnnouncementConfirmationTemplate($user, $data);
                  break;
              case WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT:
                $langCs
                      ? $this->dialog360->sendSchoolEventConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolEventConfirmationTemplate($user, $data);
                  break;
              case WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT:
                  $langCs
                      ? $this->dialog360->sendSchoolEventConfirmationTemplateCs($user, $data)
                      : $this->dialog360->sendSchoolEventConfirmationTemplate($user, $data);
                  break;
              default:
                  $this->dialog360->sendWhatsAppMessage($user->mobile_number, 'Unknown school business message type.');
                  break;
            }
          } elseif ($isRestaurant) {
            switch ($businessChat->message_type) {
                case WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT:
                   $langCs
                        ? $this->dialog360->sendRestaurantAnnouncementConfirmationTemplateCs($user, $data)
                        : $this->dialog360->sendRestaurantAnnouncementConfirmationTemplate($user, $data);
                    break;
                case WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT:
                    $langCs
                        ? $this->dialog360->sendRestaurantAnnouncementConfirmationTemplateCs($user, $data)
                        : $this->dialog360->sendRestaurantAnnouncementConfirmationTemplate($user, $data);
                    break;

                case WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER:
                  $langCs
                        ? $this->dialog360->sendRestaurantOfferConfirmationTemplateCs($user, $data)
                        : $this->dialog360->sendRestaurantOfferConfirmationTemplate($user, $data);
                    break;
                case WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT:
                    $langCs
                        ? $this->dialog360->sendRestaurantOfferConfirmationTemplateCs($user, $data)
                        : $this->dialog360->sendRestaurantOfferConfirmationTemplate($user, $data);
                    break;

                default:
                    $this->dialog360->sendWhatsAppMessage($user->mobile_number, 'Unknown restaurant business message type.');
                    break;
            }
          }
        }
      } else {
        $response = $this->getLangChainResponse($user, $text);
        // $this->processRegularMessage($user, $text);

        $replyMessage = $response['reply'];
        info('get normal messages reply: ' . $replyMessage);

        // Send response and store conversation
        $this->dialog360->sendWhatsAppMessage($user->mobile_number, $replyMessage);
        $this->databaseAction->storeConversation($user, $text, $replyMessage);
      }
  }
  private function handleRestaurantCapturedEntity(User $user, array $capturedEntity): ?string
  {
        $restaurant = Restaurant::find($capturedEntity['id']);

        if (!$restaurant) {
            return WhatsAppConstants::RESPONSE_RESTAURANT_NOT_FOUND;
        }
          if(isset($capturedEntity['order_booking']) && $capturedEntity['order_booking']) {
              $this->handleRestaurantDetails($user, $restaurant->id);
              return null;
          }
          elseif ( isset($capturedEntity['table_booking']) && $capturedEntity['table_booking']) {
              $this->handleRestaurantTableBooking($user, $restaurant);
              return null; // Use original reply message
          }
        $this->handleRestaurantDetails($user, $restaurant->id);

        return null; // Use original reply message
  }

  private function handleRestaurantTableBooking($user, $restaurant){

      $bodyParams = [
          $restaurant->user->name,
      ];
      $lang = $user->language_code == WhatsAppConstants::LANGUAGE_CS ? 'czech' : 'english';
      $buttonParams = [
          "restaurant/{$restaurant->id}/table_booking/{$user->id}?lang={$lang}",
      ];
      $templateName = 'restaurant_table_booking';
      $this->dialog360->sendTemplateWhatsAppMessage($user->mobile_number, $templateName, $user->language_code,$bodyParams,$buttonParams);
      $rendered = $this->dialog360->renderTemplate($templateName, $bodyParams);
      $this->databaseAction->storeConversation($user, "", $rendered);

  }

  private function handleButtonReply(User $user, array $replyText): void
  {
    if ($replyText['type'] === WhatsAppConstants::TYPE_RESTAURANT) {
      $this->handleRestaurantButtonReply($user, $replyText);
    }
  }

  private function handleRestaurantButtonReply(User $user, array $replyText): void
  {
    $restaurant = Restaurant::with('items')->find($replyText['id']);
    if (!$restaurant) {
      $this->dialog360->sendWhatsAppMessage($user->mobile_number, WhatsAppConstants::RESPONSE_RESTAURANT_NOT_FOUND);
      return;
    }

    match ($replyText['action']) {
      WhatsAppConstants::ACTION_LOCATION => $this->handleLocationAction($user, $restaurant),
      WhatsAppConstants::ACTION_MENU => $this->handleMenuAction($user, $restaurant),
      WhatsAppConstants::ACTION_CHECKOUT => $this->handleCheckoutAction($user, $restaurant),
      WhatsAppConstants::ACTION_CLEAR_CART => $this->handleClearCartAction($user, $restaurant),
      WhatsAppConstants::ACTION_VIEW_CART => $this->sendCartSummary($user, $restaurant, $replyText),
      WhatsAppConstants::ACTION_TABLE_BOOKING => $this->handleRestaurantTableBooking($user, $restaurant),
      default => null
    };
  }

  private function handleLocationAction(User $user, Restaurant $restaurant): void
  {
    $message =  $user->language_code == WhatsAppConstants::LANGUAGE_CS ? "Zde je umístění {$restaurant->user->name}:\n{$restaurant->address_link}\n"
                  : "Here is the location of {$restaurant->user->name}:\n{$restaurant->address_link}\n";
    $this->dialog360->sendWhatsAppMessage($user->mobile_number, $message);
  }

  public function handleMenuAction(User $user, Restaurant $restaurant): void
  {
    $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
      $this->dialog360->sendRestaurantMenuWhatsAppTemplateMessageCs($user, $restaurant) :
      $this->dialog360->sendRestaurantMenuWhatsAppTemplateMessage($user, $restaurant);
  }

  private function handleCheckoutAction(User $user, Restaurant $restaurant): void
  {
    $cartItems = $this->getCartItems($user, $restaurant);

    if ($cartItems->isEmpty()) {
      $this->dialog360->sendWhatsAppMessage($user->mobile_number, WhatsAppConstants::RESPONSE_CART_EMPTY);
      return;
    }

    $this->processCheckout($user, $restaurant, $cartItems);
  }

  private function handleClearCartAction(User $user, Restaurant $restaurant): void
  {
    RestaurantCart::where('user_id', $user->id)
      ->where('restaurant_id', $restaurant->id)
      ->where('is_order_placed', false)
      ->delete();
    $message = $user->language_code == WhatsAppConstants::LANGUAGE_CS ? WhatsAppConstants::RESPONSE_CART_CLEARED_CZ : WhatsAppConstants::RESPONSE_CART_CLEARED;
    $this->dialog360->sendWhatsAppMessage($user->mobile_number, $message);
    $this->databaseAction->storeConversation($user, "Clear cart", $message);
  }

  private function handleListReply(User $user, array $replyText): void
  {
    if ($replyText['type'] === WhatsAppConstants::TYPE_MENU) {
      $this->handleMenuListReply($user, $replyText);
    }
  }

  private function handleMenuListReply(User $user, array $replyText): void
  {
    $menu = RestaurantMenuItem::with('restaurant')
      ->where('id', $replyText['id'])
      ->first();

    if (!$menu) {
      $this->dialog360->sendWhatsAppMessage($user->mobile_number, WhatsAppConstants::RESPONSE_RESTAURANT_NOT_FOUND);
      return;
    }

    if ($replyText['action'] === WhatsAppConstants::ACTION_ADD_ITEM) {
      $this->addItemToCart($user, $menu, $replyText);
    } elseif ($replyText['action'] === WhatsAppConstants::ACTION_VIEW) {
      if($user->language_code == WhatsAppConstants::LANGUAGE_CS) {
        $this->dialog360->sendRestaurantMenuWhatsAppTemplateMessageCs($user, $menu->restaurant);
        $this->databaseAction->storeConversation($user, $replyText['title'], "Zde je menu pro {$menu->restaurant->user->name} ....\n");
      } else {
        $this->dialog360->sendRestaurantMenuWhatsAppTemplateMessage($user, $menu->restaurant);
        $this->databaseAction->storeConversation($user, $replyText['title'], "Here is the menu for {$menu->restaurant->user->name} ....\n");
      }
    }
  }

  private function addItemToCart(User $user, RestaurantMenuItem $menu, array $replyText): void
  {
    $offer = $menu->offers()->where('starts_from', '<=', now())
      ->where('ends_at', '>=', now())
      ->first();
    $cart = RestaurantCart::firstOrNew([
      'user_id' => $user->id,
      'restaurant_id' => $menu->restaurant_id,
      'restaurant_menu_item_id' => $menu->id,
      'restaurant_offer_id' => $offer ? $offer->id : null,
      'is_order_placed' => false,
    ]);

    if ($cart->exists) {
      $cart->quantity += 1;
      // $cart->price += $menu->price;
    } else {
      $cart->quantity = 1;
      $cart->price = $menu->price;
      info('The price of the menu name ' . $menu->name . ' is ' . $menu->price . ' and menu id is ' . $menu->id);
    }

    $cart->save();
    $restaurant = Restaurant::find($menu->restaurant_id);
    $this->sendCartSummary($user, $restaurant, $replyText);
  }

  private function sendCartSummary(User $user, Restaurant $restaurant, array $replyText): void
  {
    $cartItems = $this->getCartItems($user, $restaurant);
    info('cartItems Details', ['cartItems' => $cartItems]);
    if ($cartItems->isEmpty()) {
      $this->dialog360->sendWhatsAppMessage($user->mobile_number, WhatsAppConstants::RESPONSE_CART_EMPTY);
      return;
    }

    $summary = '';
    $total = 0;
    $today = now()->toDateString();
    foreach ($cartItems as $item) {
      $offer = $item->restaurantOffer;
      $originalPrice = $item->price;
      $discountText = '';
      $finalPrice = $originalPrice;

      if ($offer &&
        $offer->starts_from <= $today &&
        $offer->ends_at >= $today
      ) {
        if ($offer->discount_type === 'percentage') {
          $discountAmount = ($originalPrice * $offer->discount) / 100;
          $finalPrice -= $discountAmount;
          $discountText = "(Discount: {$offer->discount}%)";
        } elseif ($offer->discount_type === 'fixed') {
          $finalPrice -= $offer->discount;
          $discountText = "(Discount: {$offer->discount} CZK)";
        }
      }

      $lineTotal = $finalPrice * $item->quantity;
      $total += $lineTotal;
      info('Total price is '. $total);
      $summary .= "• {$item->restaurantMenuItem->name} - " .
        WhatsAppConstants::CURRENCY_CZK . number_format($finalPrice, 2) .
        " x {$item->quantity} {$discountText}\n";
    }

    $message = "🛒 *Your Cart:*\n\n{$summary}\n\n*Total: " .
      WhatsAppConstants::CURRENCY_CZK . number_format($total, 2) . "*\n\n" .
      "Would you like to add more items or proceed to checkout?";

    if($user->language_code == WhatsAppConstants::LANGUAGE_CS) {
      $message = str_replace("Your Cart", "Váš Košík", $message);
      $message = str_replace("Total", "Celkem", $message);
      $message = str_replace("Would you like to add more items or proceed to checkout?", "Chcete přidat další položky nebo přejít k pokladně?", $message);
      $this->dialog360->sendRestaurantMenuAddMoreInteractiveButtonsCs($user, $restaurant, $message);
    } else {
      $this->dialog360->sendRestaurantMenuAddMoreInteractiveButtons($user, $restaurant, $message);
    }
    $this->databaseAction->storeConversation($user, $replyText['title'], $message);
  }

  private function getCartItems(User $user, Restaurant $restaurant)
  {
    return RestaurantCart::with('restaurantMenuItem', 'restaurant', 'restaurantOffer')
      ->where('restaurant_id', $restaurant->id)
      ->where('user_id', $user->id)
      ->where('is_order_placed', false)
      ->get();
  }

  private function processCheckout(User $user, Restaurant $restaurant, $cartItems): void
  {
    // $total = $cartItems->sum(fn ($item) => $item->price * $item->quantity);

    $total = 0;

    foreach ($cartItems as $item) {
      $offer = $item->restaurantOffer;
      $originalPrice = $item->price;
      $finalPrice = $originalPrice;

      if ($offer && ($offer->starts_from <= now() && $offer->ends_at >= now())) {
        if ($offer->discount_type === 'percentage') {
          $discountAmount = ($originalPrice * $offer->discount) / 100;
          $finalPrice -= $discountAmount;
        } elseif ($offer->discount_type === 'fixed') {
          $finalPrice -= $offer->discount;
        }
      }

      $lineTotal = $finalPrice * $item->quantity;
      $total += $lineTotal;
    }

    Stripe::setApiKey(config('cashier.secret'));

    $checkout = Session::create([
      'customer' => $user->stripe_id,
      'payment_method_types' => ['card'],
      'line_items' => [[
        'price_data' => [
          'currency' => WhatsAppConstants::CURRENCY_CZK,
          'unit_amount' => $total * WhatsAppConstants::STRIPE_CENTS_MULTIPLIER,
          'product_data' => [
            'name' => $restaurant->user->name . ' Order',
          ],
        ],
        'quantity' => 1,
      ]],
      'mode' => 'payment',
      'success_url' => route(WhatsAppConstants::ROUTE_PAYMENT_SUCCESS),
      'cancel_url' => route(WhatsAppConstants::ROUTE_PAYMENT_FAILED),
      'metadata' => [
        'type' => WhatsAppConstants::METADATA_TYPE_RESTAURANT_ORDER,
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'restaurant_cart_ids' => $cartItems->pluck('id')->toJson(),
      ],
    ]);

    $paymentCode = "/" . Str::after($checkout->url, '/pay/');
    $bodyParams = [$user->name, $total . '  ' . WhatsAppConstants::CURRENCY_CZK];
    $buttonParams = [$paymentCode];

    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      WhatsAppConstants::TEMPLATE_RESTAURANT_ORDER,
      $user->language_code,
      $bodyParams,
      $buttonParams
    );

    $rendered = $this->dialog360->renderTemplate(WhatsAppConstants::TEMPLATE_RESTAURANT_ORDER, $bodyParams);
    $this->databaseAction->storeConversation($user, "", $rendered);
  }

  private function prepareRestaurantData(Restaurant &$restaurant, $language = 'en'): Restaurant
  {
    $restaurant->sustainabilities = collect(json_decode($restaurant->sustainabilities) ?? [])
      ->map(fn ($item) => '• ' . $item)
      ->implode("\n");

    $restaurant->accessibilities = collect(json_decode($restaurant->accessibilities) ?? [])
      ->map(fn ($item) => '• ' . $item)
      ->implode("\n");
    $dayToCheck = [
      'Monday' => 'pondělí',
      'Tuesday' => 'úterý',
      'Wednesday' => 'středa',
      'Thursday' => 'čtvrtek',
      'Friday' => 'pátek',
      'Saturday' => 'sobota',
      'Sunday' => 'neděle'
    ];

    $restaurant->hours = collect($restaurant->timings)
      ->sortBy(fn ($timing) => WhatsAppConstants::WEEKDAY_ORDER[strtolower($timing->day)] ?? 999)
      ->map(function ($timing) use ($language, $dayToCheck) {
        $day = $language === WhatsAppConstants::LANGUAGE_CS ? $dayToCheck[ucfirst($timing->day)] : ucfirst($timing->day);
        if ($timing->is_closed) {
          return "• {$day}: Closed";
        }
        $start = $timing->start_time ? \Carbon\Carbon::parse($timing->start_time)->format('g:i A') : '-';
        $end = $timing->end_time ? \Carbon\Carbon::parse($timing->end_time)->format('g:i A') : '-';
        return "• {$day}: {$start} – {$end}";
      })
      ->implode("\n");

    return $restaurant;

  }

  private function parseMediaUrl(string $url): array
  {
    $parsedUrl = parse_url($url);
    parse_str($parsedUrl['query'], $queryParams);
    return [
      'relative_url' => $parsedUrl['path'] . '?' . $parsedUrl['query'],
      'mid' => $queryParams['mid'],
      'ext' => $queryParams['ext'],
      'hash' => $queryParams['hash']
    ];
  }

  private function saveAudioFile(string $mid, string $audioFile): string
  {
    $path = storage_path(WhatsAppConstants::MEDIA_STORAGE_PATH);
    if (!file_exists($path)) {
      mkdir($path, 0755, true);
    }
    $fileName = $mid . WhatsAppConstants::AUDIO_FILE_EXTENSION;
    $fullPath = $path . $fileName;
    file_put_contents($fullPath, $audioFile);
    return $fullPath;
  }

  private function cleanupAudioFile(string $filePath): void
  {
    if (file_exists($filePath)) {
      unlink($filePath);
    }
  }

  private function linkUserToBusinessFromContext(User $user,$captureEntity): void
  {

      if($captureEntity['type'] == 'school') {
          $this->linkToSchool($user, $captureEntity);
      } elseif ($captureEntity['type'] == 'restaurant') {
          $this->linkToRestaurant($user, $captureEntity);
      }
  }

  private function linkToSchool(User $user, $captureEntity): void
  {
    if (!empty($captureEntity['id'])) {
       $school = \App\Models\School::find($captureEntity['id']);

      if ($school && !$user->businessUsers->contains($school)) {
        $school->businessUsers()->firstOrCreate([
          'user_id' => $user->id,
          'added_by' => WhatsAppConstants::BUSINESS_USER_ADDED_BY_SEARCH
        ]);
      }
    }
  }

  private function linkToRestaurant(User $user, $context): void
  {
    if (!empty($captureEntity['id'])) {
        $restaurant = \App\Models\Restaurant::find($captureEntity['id']);
      if ($restaurant && !$user->businessUsers->contains($restaurant)) {
        $restaurant->businessUsers()->firstOrCreate([
          'user_id' => $user->id,
          'added_by' => WhatsAppConstants::BUSINESS_USER_ADDED_BY_SEARCH
        ]);
      }
    }
  }

  private function getLangChainResponse(User $user, string $message): array
  {
    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'x-api-key' => config('constant.langchain.x-api-key')
      ])
      ->post(config('constant.langchain.base_url') . 'ask', [
      'message' => $message,
      'user_id' => $user->id,
    ]);

    info('LangChain API Request', [
      'user_id' => $user->id,
      'message' => $message,
      'response' => $response->body(),
      'status' => $response->status()
    ]);


    if( $response->failed() || !isset($response['reply'])) {
      info('LangChain API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      return [
        'reply' => WhatsAppConstants::RESPONSE_UNDERSTANDING_ERROR,
        'captured_entity' => null,
      ];
    }

   return [
        'reply' => $response['reply'],
        'detected_language' => $response['detected_language'] ?? null,
        'captured_entity' => $response['captured_entity'] ?? null,
    ];
  }

  private function getLangChainBusinessResponse(User $user, string $message,BusinessChat $businessChat = null, bool $is_edit = false ): array
  {
    $business_type = $user->role->slug;
    match($business_type) {
      'restaurant_member' => $business_type = 'restaurant',
      'school_member' => $business_type = 'school',
      default => $business_type = $user->role->slug,
    };

    // temporary method while the edit slug is not implemented in langchain ask api.
    $messageType = match($businessChat->message_type) {
        WhatsAppConstants::SCHOOL_SOS_ADD_EDIT => WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT,
        WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT => WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT,
        WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT => WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT,
        WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT => WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT,
        WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT => WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER,
        default => $businessChat->message_type
    };
    if($messageType == WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER) {
      $userId = $user->businessMembers()
                        ->where('businessable_type', Restaurant::class)
                        ->exists()
                            ? $user->businessMembers()
                                ->where('businessable_type', Restaurant::class)
                                ->first()
                                ->businessable_id
                            : null;
    } else {
      $userId = $user->id;
    }
    info('the specific user id is '. $userId);
    info('message type is in getLangChainBusinessResponse '. $messageType);

    $response = Http::withHeaders([
        'Accept' => 'application/json',
        'x-api-key' => config('constant.langchain.x-api-key')
      ])
      ->post(config('constant.langchain.business_base_url') . 'ask', [
      'message' => $message,
      'user_id' => $userId,
      'chat_id' => $businessChat ? $businessChat->id : null,
      'type' => $businessChat ? $messageType : null,
      'business_type' => $business_type,
      'is_edit' => $is_edit
    ]);

    info('LangChain Business API Request ', [
      'user_id' => $userId,
      'message' => $message,
      'response' => $response->body(),
      'status' => $response->status()
    ]);


    if( $response->failed() || !isset($response['reply'])) {
      info('LangChainBusiness API Error', [
        'body' => $response->body(),
        'status' => $response->status()
      ]);
      return [
        'reply' => WhatsAppConstants::RESPONSE_UNDERSTANDING_ERROR,
        'captured_entity' => null,
      ];
    }

    return [
      'reply' => $response['reply'],
      'detected_language' => $response['detected_language'] ?? null,
      'extracted_data' => $response['extracted_data'] ?? null,
      'is_alert' => $response['is_alert'] ?? false,
    ];
  }


  public function sendBusinessAddPrototype(User $user,$template,$messageType): void
  {
    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      $template,
      $user->language_code
    );
    $businessChat = BusinessChat::create([
      'user_id' => $user->id,
      'data' => json_encode(['id' => null]),
      'message_type' =>$messageType
    ]);
  }

  public function sendSchoolNewSosAlertPrototype(User $user): void
  {
    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      WhatsAppConstants::TEMPLATE_SCHOOL_NEW_SOS_ALERT_PROTOTYPE,
      $user->language_code
    );
    $businessChat = BusinessChat::create([
      'user_id' => $user->id,
      'data' => json_encode(['id' => null]),
      'message_type' => WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT,
    ]);
  }

  public function addRestaurantBusinessRecord(User $user ,$data){
    if ($data['id']){
      $businessChat = BusinessChat::find($data['id']);
      if (!$businessChat) {
        $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          "Sorry, we could not identify you request."
        );
        return;
      }
      $data = json_decode($businessChat->data, true);

      if ($businessChat->message_type == WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT || $businessChat->message_type == WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT) {
        $this->databaseAction->storeConversation($user, 'confirm_announcement', 'Announcement has been sent successfully');
        $this->addRestaurantAnnouncement($user,$data);
      }
      elseif ($businessChat->message_type == WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER || $businessChat->message_type == WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT){
        $this->databaseAction->storeConversation($user, 'confirm_offers', 'Offers has been sent successfully');
        $this->addRestaurantOffer($user,$data);
      }
      $businessChat->update([
        'is_completed' => true
      ]);
    }
  }
  public function addSchoolBusinessRecord(User $user ,$data){
    if ($data['id']){
      $businessChat = BusinessChat::find($data['id']);
      info('Business Chat inside busness record', ['businessChat' => $businessChat]);
      if (!$businessChat) {
        $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          "Sorry, we could not identify you request."
        );
        return;
      }
      $data = json_decode($businessChat->data, true);


      info('Adding School Business Record', ['messageType' => $businessChat->message_type, 'data' => $data,'type' => WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT]);
      if($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT || $businessChat->message_type == WhatsAppConstants::SCHOOL_SOS_ADD_EDIT){
        info('Adding School SOS Alert', ['data' => $data]);
        $this->databaseAction->storeConversation($user, 'confirm_sos_alert', 'Sos Alert has been send successfully');
        $this->addSchoolSosAlert($user,$data);
      }
      elseif ($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT || $businessChat->message_type == WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT){
        $this->databaseAction->storeConversation($user, 'confirm_announcement', 'Announcement has been sent successfully');
        $this->addSchoolAnnouncement($user,$data);
      }
      elseif ($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT || $businessChat->message_type == WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT){
        $this->databaseAction->storeConversation($user, 'confirm_event', 'Event has been sent successfully');
        $this->addSchoolEvent($user,$data);
      }
      $businessChat->update([
        'is_completed' => true
      ]);
    }
  }
  public function editSchoolBusinessRecord(User $user ,$data){
    if ($data['id']){
      $businessChat = BusinessChat::find($data['id']);
      info('Business Chat inside busness record', ['businessChat' => $businessChat]);
      if (!$businessChat) {
        $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          "Sorry, we could not identify you request."
        );
        return;
      }

      $newMessageType = match($businessChat->message_type) {
          WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT => WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT,
          WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT => WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT,
          WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT => WhatsAppConstants::SCHOOL_SOS_ADD_EDIT,
          default => $businessChat->message_type, // fallback to current value
      };

      $businessChat->update([
          'message_type' => $newMessageType
      ]);

      $editMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Jistě, prosím poskytněte aktualizované údaje." :
        "Sure, please provide the updated details.";
      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $editMessage
      );
    }
  }

  public function editRestaurantBusinessRecord(User $user ,$data){
    if ($data['id']){
      $businessChat = BusinessChat::find($data['id']);
      info('Business Chat inside busness record', ['businessChat' => $businessChat]);
      if (!$businessChat) {
        $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          "Sorry, we could not identify you request."
        );
        return;
      }

      $newMessageType = match($businessChat->message_type) {
          WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT => WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT,
          WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER => WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT,
          default => $businessChat->message_type, // fallback to current value
      };

      $businessChat->update([
          'message_type' => $newMessageType
      ]);

      $editMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Jistě, prosím poskytněte aktualizované údaje." :
        "Sure, please provide the updated details.";

      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $editMessage
      );
    }
  }
  public function addRestaurantAnnouncement(User $user ,$data){

    try {
      $data = $data['extracted_data'] ?? [];
      $name = $user->name;
      if($user->role->slug == WhatsAppConstants::TYPE_RESTAURANT_MEMBER){
        $restaurant_member = BusinessMember::where('user_id', $user->id)->first();
        $restaurant = Restaurant::where('id', $restaurant_member->businessable_id)->first();
        $name = $restaurant->user->name;

        info('Restaurant Name', ['name' => $name]);
        // $restaurant = $user->restaurantMember()->first();
      }
      else $restaurant = $user->restaurant;

      $announcement = Annoucement::create([
        'businessable_id' => $restaurant->id,
        'member_id' => $user->id,
        'businessable_type' => Restaurant::class,
        'title' => $data['title'] ?? '',
        'description' => $data['description'] ?? '',
        'start_date' => $data['start_date'] ?? null,
        'end_date' => $data['end_date'] ?? null,
        'type' => $data['type'] ?? '',
      ]);

      $announcement_data = [
        'restaurant_name' => $name,
        'type' => $announcement->type,
        'title' => $announcement->title,
        'description' => $announcement->description,
        'start_date' => $announcement->start_date,
        'end_date' => $announcement->end_date,
      ];

      info('Restaurant Announcement Data', ['data' => $announcement_data]);

      $users = User::where('is_verified', true)
        ->whereHas('businessUsers', function ($q) use ($restaurant) {
          $q->where('businessable_type', Restaurant::class)
            ->where('businessable_id', $restaurant->id)
            ->where('added_by', '!=', 'search');
        })
        ->get();
      info('All users', ['users' => $users->toArray()]);
      dispatch(new \App\Jobs\Send360TemplateMessageJob(
        $users,
        'school_announcement',
        $announcement_data,
        [
          'announcement_id' => $announcement->id
        ]
      ));

      $confirmMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Oznámení bylo úspěšně odesláno." :
        "Announcement has been send successfully.";

      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $confirmMessage
      );
      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendRestaurantModuleAddMoreButtonsCs($user) :
        $this->dialog360->sendRestaurantModuleAddMoreButtons($user);
    }
    catch (\Exception $e){
      info('Error creating Announcement', ['error' => $e->getMessage()]);
    }

  }

  public function addRestaurantOffer(User $user ,$data){

    try {
      $data = $data['extracted_data'] ?? [];
      // $restaurant = $user->restaurant;
      $name = $user->name;
      if($user->role->slug == WhatsAppConstants::TYPE_RESTAURANT_MEMBER){
        $restaurant_member = BusinessMember::where('user_id', $user->id)->first();
        $restaurant = Restaurant::where('id', $restaurant_member->businessable_id)->first();
        $name = $restaurant->user->name;
        info('Restaurant Name', ['name' => $name]);
        // $restaurant = $user->restaurantMember()->first();
      }
      else $restaurant = $user->restaurant;

      $status = Carbon::parse($data['start_date'] ?? null)->isPast() ? 'active' : 'inactive';
      $offer = RestaurantOffer::create([
        'restaurant_id' => $restaurant->id,
        'member_id' => $user->id,
        'title' => $data['title'] ?? '',
        'description' => $data['description'] ?? '',
        'starts_from' => $data['start_date'] ?? null,
        'ends_at' => $data['end_date'] ?? null,
        'discount' => $data['discount'] ?? 0,
        'discount_type' => strtolower($data['discount_type']) ?? '',
        'status' => $status,
      ]);
      // Get all applicable item names
      $itemNames = $data['applicable_items'] ?? [];

      if (!empty($itemNames)) {
          // Fetch all matching menu items in a single query
          // $items = RestaurantMenuItem::where('restaurant_id', $restaurant->id)
          //     ->where(function ($query) use ($itemNames) {
          //         foreach ($itemNames as $name) {
          //             $query->orWhere('name', 'like', '%' . $name . '%');
          //         }
          //     })
          //     ->pluck('id'); // Only get the IDs

          $items = RestaurantMenuItem::where('restaurant_id', $restaurant->id)
          ->whereIn('name', $itemNames) // exact match against multiple names
          ->pluck('id');

          // Attach all at once
          if ($items->isNotEmpty()) {
              $offer->applicableItems()->attach($items->toArray());
          } else {
              info('No matching menu items found for the provided names.', ['itemNames' => $itemNames]);
          }
      }
      $applicable_items = $offer->applicableItems->count() > 0 ? $offer->applicableItems->pluck('name')->implode(', ') : 'N/A';
      $discount_type = $offer->discount_type == 'percentage' ? '%' : 'CZK';
      $restaurant_offer_json = [
        'restaurant_id' => $restaurant->id,
        'restaurant_name' => $name,
        'title' => $offer->title,
        'discount' => $offer->discount,
        'discount_type' => $discount_type,
        'description' => $offer->description,
        'valid_from' => $offer->starts_from,
        'valid_until' => $offer->ends_at,
        'applicable_items' => $applicable_items
      ];
      info('Restaurant Offer JSON', ['offer' => $restaurant_offer_json]);
      $users = User::where('is_verified', true)
        ->whereHas('businessUsers', function ($q) use ($restaurant) {
          $q->where('businessable_type', Restaurant::class)
            ->where('businessable_id', $restaurant->id)
            ->where('added_by', '!=', 'search');
        })
        ->get();

      // dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'restaurant_offers', $restaurant_offer_json, [
      //   'offer_id' => $offer->id
      // ]));
      dispatch(new \App\Jobs\SendRestaurantOfferMessageJob($users, $restaurant_offer_json));


      $confirmMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Oznámení bylo úspěšně odesláno." :
        "Offer has been added successfully.";

      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $confirmMessage
      );

      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendRestaurantModuleAddMoreButtonsCs($user) :
        $this->dialog360->sendRestaurantModuleAddMoreButtons($user);

    }
    catch (\Exception $e){
      info('Error creating Offer', ['error' => $e->getMessage()]);
    }
  }

  public function addSchoolAnnouncement(User $user ,$data){

    try {
      $sosData = $data['extracted_data'] ?? [];
      $name = $user->name;
      if($user->role->slug == WhatsAppConstants::TYPE_SCHOOL_MEMBER){
        $school_member = BusinessMember::where('user_id', $user->id)->first();
        $school = School::where('id', $school_member->businessable_id)->first();
        $name = $school->user->name;
        info('School Name', ['name' => $name]);
        // $restaurant = $user->restaurantMember()->first();
      }
      else $school = $user->school;
      $announcement = Annoucement::create([
        'businessable_id' => $school->id,
        'member_id' => $user->id,
        'businessable_type' => School::class,
        'title' => $sosData['title'] ?? '',
        'description' => $sosData['description'] ?? '',
        'start_date' => $sosData['start_date'] ?? null,
        'end_date' => $sosData['end_date'] ?? null,
        'type' => $sosData['type'] ?? '',
      ]);

      $announcement_json = [
        'school_name' => $name,
        'type' => $announcement->type,
        'title' => $announcement->title,
        'description' => $announcement->description,
        'start_date' => $announcement->start_date,
        'end_date' => $announcement->end_date,
      ];

      $users = User::where('is_verified', true)
        ->whereHas('businessUsers', function ($q) use ($school) {
          $q->where('businessable_type', School::class)
            ->where('businessable_id', $school->id)
            ->where('added_by', '!=', 'search');
        })
        ->get();
        dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'school_announcement', $announcement_json, [
          'announcement_id' => $announcement->id
        ]));

      $confirmMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Oznámení bylo úspěšně odesláno." :
        "Announcement has been send successfully.";

      $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          $confirmMessage
        );

      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendSchoolModuleAddMoreButtonsCs($user) :
        $this->dialog360->sendSchoolModuleAddMoreButtons($user);

    }
    catch (\Exception $e){
      info('Error creating Announcement', ['error' => $e->getMessage()]);
    }

  }
  public function addSchoolEvent(User $user ,$data){

    try {
      $eventData = $data['extracted_data'] ?? [];
      $name = $user->name;
      if($user->role->slug == WhatsAppConstants::TYPE_SCHOOL_MEMBER){
        $school_member = BusinessMember::where('user_id', $user->id)->first();
        $school = School::where('id', $school_member->businessable_id)->first();
        $name = $school->user->name;
        info('School Name', ['name' => $name]);
        // $restaurant = $user->restaurantMember()->first();
      }
      else $school = $user->school;
      $event = SchoolEvent::create([
        'school_id' => $school->id,
        'member_id' => $user->id,
        'title' => $eventData['title'] ?? '',
        'description' => $eventData['description'] ?? '',
        'start_date' => $eventData['start_date'] ?? null,
        'end_date' => $eventData['end_date'] ?? null,
        'type' => $eventData['type'] ?? '',
      ]);

      $confirmMessage =  $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "Událost byla úspěšně odeslána." :
        "Event has been send successfully.";

      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $confirmMessage
      );

      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendSchoolModuleAddMoreButtonsCs($user) :
        $this->dialog360->sendSchoolModuleAddMoreButtons($user);

    }
    catch (\Exception $e){
      info('Error creating Event', ['error' => $e->getMessage()]);
    }

  }
  public function addSchoolSosAlert(User $user ,$data){

    try {

      $sosData = $data['extracted_data'] ?? [];
      // $school = $user->school;
      $name = $user->name;
      if($user->role->slug == WhatsAppConstants::TYPE_SCHOOL_MEMBER){
        $school_member = BusinessMember::where('user_id', $user->id)->first();
        $school = School::where('id', $school_member->businessable_id)->first();
        $name = $school->user->name;
        info('School Name', ['name' => $name]);
        // $restaurant = $user->restaurantMember()->first();
      }
      else $school = $user->school;

      $sos = SchoolSosAlert::create([
        'school_id' => $school->id,
        'member_id' => $user->id,
        'title' => $sosData['title'] ?? '',
        'message' => $sosData['message'] ?? '',
        'type' => $sosData['type'] ?? '',
      ]);
      // $announcement_json = [
      //   'school_name' => $name,
      //   'title' => $sos->title,
      //   'description' => $sos->message,
      //   'type' =>  $sos->type ? SchoolSosAleart::from($sos->type)->label() : '',
      // ];
      info('SOS Alert Data', ['sos' => $sos]);
      $announcement_json = [
        'school_name' => $school->user->name,
        'title' => $sos->title,
        'description' => $sos->message,
        'type' => SchoolSosAleart::safeFrom($sos->type) ?? '',
      ];
      $users = User::where('is_verified', true)
        ->whereHas('businessUsers', function ($q) use ($school) {
          $q->where('businessable_type', School::class)
            ->where('businessable_id', $school->id)
            ->where('added_by', '!=', 'search');
        })
        ->get();
      dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'alert_notification', $announcement_json, [
        'sos_id' => $sos->id
      ]));
      $confirmMessage = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        "SOS upozornění bylo úspěšně odesláno." :
        "SOS Alert has been send successfully.";

      $this->dialog360->sendWhatsAppMessage(
        $user->mobile_number,
        $confirmMessage
      );

      $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
        $this->dialog360->sendSchoolModuleAddMoreButtonsCs($user) :
        $this->dialog360->sendSchoolModuleAddMoreButtons($user);

    }
    catch (\Exception $e){
      info('Error creating SOS Alert', ['error' => $e->getMessage()]);
    }
  }

  public function cancelBussinessChat(User $user ,$data){
    if ($data['id']){
      $businessChat = BusinessChat::find($data['id']);

      if ($businessChat){
        $businessChat->delete();
      }
      info('message type is in cancelBusinessChat '. $businessChat->message_type);
      info('user role is in cancelBusinessChat '. $user->role->slug);

      $messageType = null;
      if ($user->role->slug == 'school' || $user->role->slug == 'school_member'){
        if ($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT || $businessChat->message_type == WhatsAppConstants::SCHOOL_SOS_ADD_EDIT){
        //  $messageType = "SOS Alert has been cancelled successfully.";
         $messageType = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
           "SOS upozornění bylo úspěšně zrušeno." :
           "SOS Alert has been cancelled successfully.";
        }
        elseif ($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT || $businessChat->message_type == WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT){
          // $messageType = "Announcement has been cancelled successfully.";
          $messageType = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
            "Oznámení bylo úspěšně zrušeno." :
            "Announcement has been cancelled successfully.";
        }
        elseif ($businessChat->message_type == WhatsAppConstants::SCHOOL_BUSINESS_ADD_NEW_EVENT || $businessChat->message_type == WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT){
          // $messageType = "Event has been cancelled successfully.";
          $messageType = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
            "Událost byla úspěšně zrušena." :
            "Event has been cancelled successfully.";
        }
      }
      elseif( $user->role->slug == 'restaurant' || $user->role->slug == 'restaurant_member'){
        if ($businessChat->message_type == WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT || $businessChat->message_type == WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT){
          // $messageType = "Announcement has been cancelled successfully.";
          $messageType = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
            "Oznámení bylo úspěšně zrušeno." :
            "Announcement has been cancelled successfully.";
        }
        elseif ($businessChat->message_type == WhatsAppConstants::RESTAURANT_BUSINESS_ADD_NEW_OFFER || $businessChat->message_type == WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT){
          // $messageType = "Offer has been cancelled successfully.";
          $messageType = $user->language_code == WhatsAppConstants::LANGUAGE_CS ?
            "Nabídka byla úspěšně zrušena." :
            "Offer has been cancelled successfully.";
        }
      }
      else{
        $messageType = null;
      }
      info('message Type is in cancelBusinessChat '. $messageType);
      if($messageType)
      {
        $this->dialog360->sendWhatsAppMessage(
          $user->mobile_number,
          $messageType
        );
        info('message sent to user');
        if ($user->role->slug == 'school' || $user->role->slug == 'school_member'){
          $user->language_code == WhatsAppConstants::LANGUAGE_CS
            ? $this->dialog360->sendSchoolModuleAddMoreButtonsCs($user)
            : $this->dialog360->sendSchoolModuleAddMoreButtons($user);
        }
        if ($user->role->slug == 'restaurant' || $user->role->slug == 'restaurant_member') {
          $user->language_code == WhatsAppConstants::LANGUAGE_CS
            ? $this->dialog360->sendRestaurantModuleAddMoreButtonsCs($user)
            :  $this->dialog360->sendRestaurantModuleAddMoreButtons($user);
        }
      }
    }
  }
//
}
