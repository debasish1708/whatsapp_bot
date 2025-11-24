<?php

namespace App\Actions;

use App\Models\User;
use App\Models\WhatsAppChat;

class DatabaseAction
{
  public function storeConversation(User $user, $requestText = null, string $responseText)
  {
    WhatsAppChat::create([
      'user_id' => $user->id,
      'request' => $requestText,
      'response' => $responseText,
      'response_id' => null, // Could be populated from OpenAI if needed
    ]);
  }
}
