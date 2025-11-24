<?php

namespace App\Services;

use App\Constants\WhatsAppConstants;
use App\Dialog360\DataTransferObjects\Dialog360WebhookDTO;
use App\Dialog360\Dialog360;
use App\Models\Role;
use App\Models\User;

class UserService
{
  private Dialog360 $dialog360;

  public function __construct(Dialog360 $dialog360)
  {
    $this->dialog360 = $dialog360;
  }

  public function getOrCreateUser(Dialog360WebhookDTO $webhookDTO, MessageValidator $messageValidator = null): User
  {
    $user = User::where('mobile_number', $webhookDTO->waId)->first();

    if (!$user) {
      $user = $this->createUser($webhookDTO);
      if ($webhookDTO->text != null &&  ($messageValidator && !$messageValidator->isGreeting($webhookDTO->text))) {
        $this->sendWelcomeMessage($user);
      }
      $this->sendCitioPolicyMessage($user);
    }
    // this is only happend when the button reply is additem and thats time i dont want to change the language
    if($webhookDTO->languageCode) {
      $user->update([
        'language_code' => $webhookDTO->languageCode ?? WhatsAppConstants::LANGUAGE_EN,
      ]);
    } else {
      $webhookDTO->languageCode = $user->language_code;
    }
    return $user;
  }

  private function createUser(Dialog360WebhookDTO $webhookDTO): User
  {
    $role = Role::where('slug', WhatsAppConstants::ROLE_USER)->first();

    return User::create([
      'name' => $webhookDTO->contactName ?? 'User',
      'mobile_number' => $webhookDTO->waId,
      'role_id' => $role->id
    ]);
  }

  private function sendWelcomeMessage(User $user): void
  {
    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      WhatsAppConstants::TEMPLATE_NEW_USER,
      $user->language_code ?: WhatsAppConstants::LANGUAGE_EN
    );

  }
  private function sendCitioPolicyMessage(User $user): void
  {
    $this->dialog360->sendTemplateWhatsAppMessage(
      $user->mobile_number,
      WhatsAppConstants::TEMPLATE_CITIO_POLICY,
      $user->language_code ?: WhatsAppConstants::LANGUAGE_EN
    );
  }
}
