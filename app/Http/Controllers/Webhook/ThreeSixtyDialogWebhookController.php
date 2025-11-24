<?php

namespace App\Http\Controllers\Webhook;

use App\Actions\DatabaseAction;
use App\Constants\WhatsAppConstants;
use App\Dialog360\DataTransferObjects\Dialog360WebhookDTO;
use App\Dialog360\Dialog360;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use App\OpenAI\OpenAI;
use App\Services\MessageHandlers;
use App\Services\MessageValidator;
use App\Services\UserService;
use App\Services\WebhookService;
use Illuminate\Http\Request;

use Exception;

class ThreeSixtyDialogWebhookController extends Controller
{
  protected Dialog360  $dialog360;
  protected OpenAI  $openAI;
  private MessageValidator $messageValidator;
  private MessageHandlers $messageHandlers;
  private UserService $userService;
  private WebhookService $webhookService;

  private DatabaseAction $databaseAction;
  public function __construct()
  {
    $this->dialog360 = new Dialog360();
    $this->openAI = new OpenAI();
    $this->databaseAction = new DatabaseAction();
    $this->messageValidator = new MessageValidator($this->dialog360);
    $this->messageHandlers = new MessageHandlers($this->dialog360, $this->openAI, $this->databaseAction);
    $this->userService = new UserService($this->dialog360);
    $this->webhookService = new WebhookService();
  }

  public function __invoke(Request $request)
  {
    try {
      $data = $request->all();
      info('Received webhook', ['request' => $data]);
      $webhookDTO = new Dialog360WebhookDTO($data);
      info('Parsed webhook DTO', ['webhookDTO' => $webhookDTO->toArray()]);

      // Validate webhook
      if ($this->messageValidator->isDuplicateWebhook($webhookDTO->id)) {
        info('Duplicate webhook received', ['request' => $data]);
        return response()->json(['message' => WhatsAppConstants::RESPONSE_DUPLICATE_WEBHOOK], 200);
      }

      $this->webhookService->storeWebhook($request->headers->all(), $webhookDTO->toArray());

      if (!$this->messageValidator->isValidWebhook($request)) {
        info('Invalid webhook token.');
        return response()->json(['error' => WhatsAppConstants::RESPONSE_INVALID_TOKEN], 200);
      }

      if (!$this->messageValidator->isValidMessagingProduct($webhookDTO->messagingProduct)) {
        info('Non-WhatsApp webhook received.');
        return response()->json(['error' => WhatsAppConstants::RESPONSE_INVALID_MESSAGING_PRODUCT], 200);
      }

      if (!$this->messageValidator->hasRequiredData($webhookDTO)) {
        return response()->json(['error' => WhatsAppConstants::RESPONSE_MISSING_DATA], 200);
      }

      // Get or create user
      $user = $this->userService->getOrCreateUser($webhookDTO,$this->messageValidator);

      // Handle greeting messages
      if ($webhookDTO->text != null &&  $this->messageValidator->isGreeting($webhookDTO->text)) {
        $this->messageHandlers->handleGreeting($user, $webhookDTO->text, $user->language_code);
        return response(WhatsAppConstants::RESPONSE_SUCCESS, 200);
      }

      if(in_array($user->role->slug, ['school', 'school_member', 'restaurant', 'restaurant_member'])) {
          $this->handleBusinessMessage($user, $webhookDTO);
          return response(WhatsAppConstants::RESPONSE_SUCCESS, 200);
      }

      // Validate message type
      if (!$this->messageValidator->isSupportedMessageType($webhookDTO->messageType)) {
        info('Unsupported message type: ' . $webhookDTO->messageType);
        return response()->json(['error' => WhatsAppConstants::RESPONSE_UNSUPPORTED_MESSAGE_TYPE], 200);
      }


      $this->routeMessage($user, $webhookDTO);


      return response(WhatsAppConstants::RESPONSE_SUCCESS, 200);

    } catch (Exception $e) {
      info('Error in webhook: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
      return response()->json(['error' => WhatsAppConstants::RESPONSE_INTERNAL_ERROR], 500);
    }
  }



  private function handleBusinessMessage(User $user, $webhookDTO): void
  {
    match ($webhookDTO->messageType) {
      WhatsAppConstants::MESSAGE_TYPE_INTERACTIVE => $this->messageHandlers->handleBusinessInteractiveMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_TEXT => $this->handleBusinessTextMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_AUDIO => $this->messageHandlers->handleAudioMessage($user, $webhookDTO),
      default => null
    };
  }
  private function routeMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    match ($webhookDTO->messageType) {
      WhatsAppConstants::MESSAGE_TYPE_INTERACTIVE => $this->messageHandlers->handleInteractiveMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_LOCATION => $this->messageHandlers->handleLocationMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_AUDIO => $this->messageHandlers->handleAudioMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_BUTTON => $this->handleButtonMessage($user, $webhookDTO),
      WhatsAppConstants::MESSAGE_TYPE_TEXT => $this->handleTextMessage($user, $webhookDTO),
      default => null
    };
  }

  private function handleButtonMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    if($webhookDTO->isAskForMenu) {
      if($webhookDTO->buttonReplyForOrder && !empty($webhookDTO->buttonReplyForOrder['id'])) {
        $restaurant = Restaurant::find($webhookDTO->buttonReplyForOrder['id']);
        $this->messageHandlers->handleMenuAction($user, $restaurant);
      } else {
        info('Button reply', ['button_reply' => $webhookDTO->buttonReplyForOrder]);
      }
      return;
    }
    $processedText = $this->messageHandlers->processButtonText($webhookDTO->buttonText);
    $this->messageHandlers->processRegularMessage($user, $processedText);
  }

  private function handleTextMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    $this->messageHandlers->processRegularMessage($user, $webhookDTO->text);
  }
  private function handleBusinessTextMessage(User $user, Dialog360WebhookDTO $webhookDTO): void
  {
    $this->messageHandlers->processBusinessMessage($user, $webhookDTO->text);
  }

}
