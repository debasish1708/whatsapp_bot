<?php

namespace App\Services;

use App\Constants\WhatsAppConstants;
use App\Dialog360\DataTransferObjects\Dialog360WebhookDTO;
use App\Dialog360\Dialog360;
use App\Models\Webhook;
use Illuminate\Http\Request;

class MessageValidator
{
  private Dialog360 $dialog360;

  public function __construct(Dialog360 $dialog360)
  {
    $this->dialog360 = $dialog360;
  }

  public function isDuplicateWebhook(string $id): bool
  {
    return Webhook::where('webhook_id', $id)->exists();
  }

  public function isValidWebhook(Request $request): bool
  {
    return $this->dialog360->verifyWebhook($request);
  }

  public function isValidMessagingProduct(string $messagingProduct): bool
  {
    return $messagingProduct === WhatsAppConstants::MESSAGING_PRODUCT_WHATSAPP;
  }

  public function hasRequiredData(Dialog360WebhookDTO $webhookDTO): bool
  {
    return !empty($webhookDTO->waId) &&
      (!empty($webhookDTO->text) || !empty($webhookDTO->messageType));
  }

  public function isSupportedMessageType(string $messageType): bool
  {
    return in_array($messageType, WhatsAppConstants::SUPPORTED_MESSAGE_TYPES);
  }

  public function isGreeting(string $text): bool
  {
    $lowerText = strtolower($text);
    return str_starts_with($lowerText, WhatsAppConstants::GREETING_HELLO) ||
      str_starts_with($lowerText, WhatsAppConstants::GREETING_HI) ||
      str_starts_with($lowerText, WhatsAppConstants::GREETING_HI_CS);
  }

  public function isSpecialRestaurantRequest(string $text): bool
  {
    return $text === WhatsAppConstants::SPECIAL_RESTAURANT_TEXT;
  }
}
