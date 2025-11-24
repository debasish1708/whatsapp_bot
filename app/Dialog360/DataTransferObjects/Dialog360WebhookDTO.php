<?php

namespace App\Dialog360\DataTransferObjects;

use Illuminate\Support\Facades\Log;

class Dialog360WebhookDTO
{
  public $object;
  public $entryId;
  public $field;
  public $messagingProduct;
  public $displayPhoneNumber;
  public $phoneNumberId;
  public $contactName;
  public $waId;
  public $messageId;
  public $messageType;
  public $timestamp;
  public $text;
  public $location;
  public $id;

  public $buttonText;
  public $buttonPayload;

  public $audioId;

  public $interactiveType;
  public $buttonReply;

  public $languageCode;
  public $isAskForMenu;
  public $buttonReplyForOrder;
  public function __construct(array $payload)
  {


    $entry = $payload['entry'][0] ?? [];
    $change = $entry['changes'][0] ?? [];
    $value = $change['value'] ?? [];

    $this->id = $value['messages'][0]['id'] ?? $value['statuses'][0]['id'] ?? null;

    $this->object = $payload['object'] ?? null;
    $this->entryId = $entry['id'] ?? null;
    $this->field = $change['field'] ?? null;

    $this->messagingProduct = $value['messaging_product'] ?? null;
    $this->displayPhoneNumber = $value['metadata']['display_phone_number'] ?? null;
    $this->phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
    $this->contactName = $value['contacts'][0]['profile']['name'] ?? null;
    $this->waId = $value['contacts'][0]['wa_id'] ?? null;

    $message = $value['messages'][0] ?? [];
    $this->messageId = $message['id'] ?? null;
    $this->messageType = $message['type'] ?? null;
    $this->timestamp = $message['timestamp'] ?? null;

    // Text message
    $this->text = $message['text']['body'] ?? null;


    if ($this->messageType == 'audio'){
      $this->audioId = $message['audio']['id'] ?? null;
    } else {
      $this->audioId = null; // Clear audio ID if not an audio message
    }

    if ($this->messageType == 'button'){
      $this->buttonText = $message['button']['text'] ?? null;
      $this->buttonPayload = $message['button']['payload'] ?? null;
      $this->text = $message['button']['text'] ?? null;
      if($this->buttonPayload && ($this->buttonText == 'Order Now' || $this->buttonText == 'Objednat nyní')) {
        [$restaurantId,$type,$action] = explode('_', $this->buttonPayload, 3);
        if($restaurantId && $type && $action) {
          $this->isAskForMenu = true;
          $this->buttonReplyForOrder = [
            'id' => $restaurantId,
            'type' => $type,
            'action' => $action,
            'title' => 'send menu details',
          ];
        }
      }
    } else {
      $this->buttonText = null;
      $this->buttonPayload = null;
    }

    if ($this->messageType == 'interactive'){
      $this->interactiveType = $message['interactive']['type'] ?? null;
      if (isset($message['interactive']['button_reply'])) {
        [$restaurantId,$type,$action] = explode('_', $message['interactive']['button_reply']['id'], 3);
        $this->buttonReply = [
          'id' => $restaurantId ?? null,
          'type' => $type ?? null,
          'action' => $action ?? null,
          'title' => $message['interactive']['button_reply']['title'] ?? null,
        ];
        $this->text = $message['interactive']['button_reply']['title'] ?? null; // Use button title as text

      }
     elseif (isset($message['interactive']['list_reply']))
     {

        [$menuId, $type, $action] = explode('_', $message['interactive']['list_reply']['id'], );
        $this->buttonReply = [
          'id' => $menuId ?? null,
          'type' => $type ?? null,
          'action' => $action ?? null,
          'title' => $message['interactive']['list_reply']['title'] ?? null,
        ];
        $this->text = $message['interactive']['list_reply']['title'] ?? null; // Use list reply title as text
      } else {
        $this->interactiveType = null;
        $this->buttonReply = null; // Clear button reply if not an interactive message
     }
    } else {
      $this->buttonReply = null; // Clear button reply if not an interactive message
    }

    // Location message
    if (isset($message['location'])) {
      $this->location = [
        'address' => $message['location']['address'] ?? null,
        'name' => $message['location']['name'] ?? null,
        'latitude' => $message['location']['latitude'] ?? null,
        'longitude' => $message['location']['longitude'] ?? null,
      ];
    } else {
      $this->location = null;
    }

    if ($this->text !== null) {
      // if(isset($this->buttonReply) && $this->buttonReply['type'] == 'menu' && $this->buttonReply['action'] == 'additem') {
      //   $this->languageCode = null;
      // }
      if (is_array($this->buttonReply)
          && isset($this->buttonReply['type'], $this->buttonReply['action'])
          && $this->buttonReply['type'] === 'menu'
          && $this->buttonReply['action'] === 'additem') {

          $this->languageCode = null;
      } else {
        $this->languageCode = $this->getLanguageCode();
      }
    } else {
      $this->languageCode = 'en'; // Clear language code if text is not set
    }

  }

  public function getLanguageCode(): ?string
  {
    $ld = new \LanguageDetection\Language(['en','cs']);

    $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $this->text);
    $cleanText = trim($cleanText);
    if($cleanText == 'Book Table' || $cleanText == 'Checkout' || $cleanText == 'View Menu' || $cleanText == 'Order Now') {
      return 'en';
    }

    $results = $ld->detect($this->text)->bestResults()->jsonSerialize();

    return !empty($results) ? array_key_first($results) : 'en';

    // Clean text
    // $cleanText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $this->text);
    // $cleanText = trim($cleanText);
    // if($cleanText == 'Book Table' || $cleanText == 'Checkout') {
    //   return 'en';
    // }
    // $results = $ld->detect($cleanText)->close();

    // if ($results) {
    //     $enScore = $results['en'] ?? 0;
    //     $csScore = $results['cs'] ?? 0;

    //     if ($csScore > 0.41 && $csScore > $enScore) {
    //         $lang = 'cs';
    //     } else {
    //         $lang = 'en';
    //     }
    // } else {
    //     $lang = 'en'; // fallback
    // }

    // return $lang;

  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'object' => $this->object,
      'entry_id' => $this->entryId,
      'field' => $this->field,
      'messaging_product' => $this->messagingProduct,
      'display_phone_number' => $this->displayPhoneNumber,
      'phone_number_id' => $this->phoneNumberId,
      'contact_name' => $this->contactName,
      'wa_id' => $this->waId,
      'message_id' => $this->messageId,
      'message_type' => $this->messageType,
      'timestamp' => $this->timestamp,
      'text' => $this->text,
      'button_text' => $this->buttonText,
      'button_payload' => $this->buttonPayload,
      'location' => $this->location,
      'audio_id' => $this->audioId,
      'interactive_type' => $this->interactiveType,
      'button_reply' => $this->buttonReply,
      'language_code' => $this->languageCode,
    ];
  }
}
