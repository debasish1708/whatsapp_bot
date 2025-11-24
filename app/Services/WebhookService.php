<?php

namespace App\Services;

use App\Constants\WhatsAppConstants;
use App\Models\Webhook;

class WebhookService
{
  public function storeWebhook(array $headers, array $payload): void
  {
    Webhook::create([
      'vendor' => WhatsAppConstants::WEBHOOK_VENDOR,
      'webhook_id' => $payload['id'] ?? null,
      'header' => json_encode($headers),
      'payload' => json_encode($payload),
    ]);
  }
}
