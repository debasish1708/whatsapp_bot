<?php

namespace App\Enums;

enum WebhookConstants: string
{
  // Messaging Product
  case WHATSAPP_PRODUCT = 'whatsapp';

  // Triggers
  case RESTAURANT_DETAIL_TRIGGER = 'RESTAURANT_DETAIL';

  // Greeting template name
  case GREETING_TEMPLATE = 'restaurant_greeting';

  // Default button text fallback
  case DEFAULT_BUTTON_TEXT = 'View Menu';

  // Default error reply
  case DEFAULT_ERROR_REPLY = 'Sorry, I couldn\'t understand that. Can you rephrase?';

  // Supported message types
  public static function supportedMessageTypes(): array
  {
    return [
      'text',
      'interactive',
      'location',
      'audio',
      'button',
    ];
  }

  // Button text mappings
  public static function buttonTextMapping(): array
  {
    return [
      'View Menu' => 'show_menu',
      'Order Now' => 'order_now',
      'Get Directions' => 'get_directions',
      'Call Restaurant' => 'call_restaurant',
    ];
  }
}
