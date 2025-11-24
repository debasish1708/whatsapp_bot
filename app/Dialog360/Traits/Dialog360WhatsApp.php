<?php
namespace App\Dialog360\Traits;

use App\Constants\WhatsAppConstants;
use App\Models\Restaurant;
use App\Models\RestaurantCart;
use App\Models\School;
use App\Models\SchoolEvent;
use App\Models\User;
use App\Models\WhatsAppChat;
use Http;
use Illuminate\Support\Str;

trait Dialog360WhatsApp
{

  /**
   * Send a simple text message via WhatsApp
   */
  public function sendWhatsAppMessage(string $mobile, string $message): mixed
  {
    $data = [
      "messaging_product" => "whatsapp",
      "recipient_type" => "individual",
      'to' => $mobile,
      'type' => 'text',
      'text' => ['body' => $message]
    ];

    info('Sending WhatsApp message', ['data' => $data]);
    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }


  /**
   * Send a template message via WhatsApp
   */
  public function sendTemplateWhatsAppMessage(
    string $mobile,
    string $templateName,
    string $languageCode,
    array $parameters = [],
    array $buttonParameters = []
  ): mixed {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $mobile,
      'type' => 'template',
      'template' => [
        'name' => $templateName,
        'language' => ['code' => $languageCode],
      ],
    ];

    // Body parameters
    if (!empty($parameters)) {
      $data['template']['components'][] = [
        'type' => 'body',
        'parameters' => $this->formatTextParameters($parameters),
      ];
    }

    // Button parameters (e.g., URL button)
    if (!empty($buttonParameters)) {
      $data['template']['components'][] = [
        'type' => 'button',
        'sub_type' => 'url',
        'index' => '0',
        'parameters' => $this->formatTextParameters($buttonParameters),
      ];
    }

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }


  /**
   * Send a location request message
   */
  public function sendWhatsAppLocationRequestMessage(string $userMobile): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'recipient_type' => 'individual',
      'type' => 'interactive',
      'to' => $userMobile,
      'interactive' => [
        'type' => 'location_request_message',
        'body' => [
          'text' => 'Please Share Your Location. You can either manually *enter an address* or *share your current location*.'
        ],
        'action' => ['name' => 'send_location']
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }
  /**
   * Get WhatsApp media by ID
   */
  public function getWhatsAppMedia(string $mediaId): mixed
  {
    return $this->getMediaApi('/' . $mediaId);
  }

  /**
   * Download WhatsApp media file
   */
  public function downloadWhatsAppMedia(string $mediaUrl): mixed
  {
    return $this->downloadMediaFile($mediaUrl);
  }

  /**
   * Send restaurant information as interactive message
   */
  public function sendRestaurantWhatsAppTemplateMessage(User $user, Restaurant $restaurant): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'header' => [
          'type' => 'image',
          'image' => [
            'link' => $restaurant->logo ?? asset('assets/img/restaurant_placeholder.png')
          ]
        ],
        'body' => [
          'text' => $this->formatRestaurantDetails($restaurant)
        ],
        'footer' => [
          'text' => 'Choose an option below'
        ],
        'action' => [
          'buttons' => $this->buildRestaurantTemplateButton($user,$restaurant)
        ]
      ]
    ];
info("Sending restaurant WhatsApp template message", ['data' => $data]);
    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant menu as interactive list
   */
  public function sendRestaurantMenuWhatsAppTemplateMessage(User $user, Restaurant $restaurant): mixed
  {
    $sections = $this->buildMenuSections($restaurant);

    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'header' => [
          'type' => 'text',
          'text' => "🍽 {$restaurant->user->name} Menu"
        ],
        'body' => [
          'text' => "Tap an item to add to your order"
        ],
        'action' => [
          'button' => "Menu Items",
          'sections' => $sections
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send cart management buttons
   */
  public function sendRestaurantMenuAddMoreInteractiveButtons(User $user, Restaurant $restaurant, string $cart): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'header' => [
          'type' => 'text',
          'text' => "🍽 {$restaurant->user->name} Cart"
        ],
        'body' => ['text' => $cart],
        'action' => [
          'buttons' => $this->buildCartActionButtons($restaurant->id)
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Format text parameters for template messages
   */
  // private function formatTextParameters(array $parameters): array
  // {
  //   return collect($parameters)->map(fn($value) => [
  //     'type' => 'text',
  //     'text' => $value
  //   ])->toArray();
  // }

  private function formatTextParameters(array $parameters): array
  {
      return array_map(function ($param) {
          return [
              'type' => 'text',
              'text' => (string) $param
          ];
      }, $parameters);
  }

  private function buildRestaurantTemplateButton(User $user, Restaurant $restaurant){
    $cart = RestaurantCart::query()
      ->where('restaurant_id', $restaurant->id)
      ->where('user_id', $user->id)
      ->where('is_order_placed', false)
      ->count();

    $buttons =  [
      [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_menu',
          'title' => '🍽 View Menu'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_tablebooking',
          'title' => '📅 Book Table'
        ]
      ],
    ];

    if ($cart > 0) {
      $buttons[] = [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_viewcart',
          'title' => "🛒 View Cart ({$cart})"
        ]
      ];
    }
    else{
      $buttons[] = [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_location',
          'title' => '📍 View Location'
        ]
      ];
    }

    return $buttons;
  }
  /**
   * Format restaurant details for display
   */
  private function formatRestaurantDetails(Restaurant $restaurant): string
  {
    $details = [
      "*{$restaurant->user->name}*",
      "🍽 Type: {$restaurant->categories->implode('name', ', ')}",
      "🌶️ Cuisines: {$restaurant->cuisines->implode('name', ', ')}",
      "📞 Phone: {$restaurant->mobile_number}",
      "📍 Address: {$restaurant->address}",
    ];

    // Add optional fields only if they exist
    if ($restaurant->sustainabilities) {
      $details[] = "🚹 Sustainability: \n{$restaurant->sustainabilities}";
    }

    if ($restaurant->accessibilities) {
      $details[] = "✅ Accessibility: \n{$restaurant->accessibilities}";
    }

    if ($restaurant->hours) {
      $details[] = "⏱️ Hours: \n{$restaurant->hours}";
    }

    return implode("\n", $details);
  }

  /**
   * Build menu sections for interactive list
   */
  private function buildMenuSections(Restaurant $restaurant): array
  {
    $itemsGroupedByCategory = $restaurant->items
      ->load('category')
      ->groupBy(fn($item) => $item->category->name ?? 'Others');

    return $itemsGroupedByCategory->map(function ($items, $categoryName) {
      $rows = $items->map(function ($item) {
        $offer = $item->offers()->where('starts_from', '<=', now())
          ->where('ends_at', '>=', now())
          ->first();
        if($offer &&  $offer->discount_type = 'percentage')
        {
          $item->price = $item->price - ($item->price * $offer->discount / 100);
          $item->description = "(Discount: {$offer->discount}%)". " " . $item->description;
        }
        elseif($offer && $offer->discount_type == 'fixed')
        {
          if ($item->price < $offer->discount) {
            $item->price = 0; // Ensure price doesn't go negative
          } else {
            $item->price = $item->price - $offer->discount;
          }
          $item->description = "(Discount: {$offer->discount})". " " . $item->description;
        }
        return [
          'id' => "{$item->id}_menu_additem",
          'title' => Str::limit($item->name, 18, '') . " - {$item->price}",
          'description' => Str::limit(trim($item->description), 70, '..'),
        ];
      });

      return [
        'title' => Str::limit($categoryName, 24, ''),
        'rows' => $rows->toArray()
      ];
    })->values()->toArray();
  }

  /**
   * Build cart action buttons
   */
  private function buildCartActionButtons(string $restaurantId): array
  {
    return [
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_menu",
          'title' => '➕ Add More'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_checkout",
          'title' => '🛒 Checkout'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_clearcart",
          'title' => '🗑️ Clear Cart'
        ]
      ]
    ];
  }




  public function sendSchoolModuleButtons(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Hello, I’m Citio, your friendly school assistant chatbot 🤖✨.\n"
            . "Now you can Add Sos Alert, Events, and announcements directly to your students 📢.\n"
            . "Take full advantage of all Citio services to connect, engage, and communicate instantly 🤝✨.\n\n"
            . "Just select an option below to start using all the features of Citio!"
        ],
        'action' => [
          'button' => 'Select an Action', // The main button text
          'sections' => [
            [
              'title' => 'Quick Actions',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Add New SOS Alert',
                  'description' => 'Send an urgent alert to students and staff'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Add New Announcement',
                  'description' => 'Create and share important announcements'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Add New Event',
                  'description' => 'Create and share upcoming events'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendSchoolModuleAddMoreButtons(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Ready to make another update? You can easily add more SOS Alerts, Announcements, or Events to keep your school community informed and engaged. 📢✨\n\n"
            . "Just select an option below to continue using all the features of Citio!"
        ],
        'action' => [
          'button' => 'Select an Action', // The main button text
          'sections' => [
            [
              'title' => 'Quick Actions',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Add New SOS Alert',
                  'description' => 'Send an urgent alert to students and staff'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Add New Announcement',
                  'description' => 'Create and share important announcements'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Add New Event',
                  'description' => 'Create and share upcoming events'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendSchoolSosConfirmationTemplate(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Type: {$data['type']}\n"
            . "Title: {$data['title']}\n"
            . "Message: {$data['message']}\n\n"
            . "Please confirm if you want to add sos alert."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_SOS_ADD_CONFIRM,
                'title' => '✅ Confirm'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_SOS_ADD_EDIT,
                'title' => '✏️ Edit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_SOS_ADD_CANCEL,
                'title' => '❌ Cancel'
              ]
            ]
          ]
        ]
      ]
    ];


    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendSchoolAnnouncementConfirmationTemplate(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Type: {$data['type']}\n"
            . "Title: {$data['title']}\n"
            . "Description: {$data['description']}\n\n"
            . "Start Date: {$data['start_date']}\n"
            . "End Date: {$data['end_date']}\n\n"
            . "Please confirm if you want to add announcement."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CONFIRM,
                'title' => '✅ Confirm'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT,
                'title' => '✏️ Edit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CANCEL,
                'title' => '❌ Cancel'
              ]
            ]
          ]
        ]
      ]
    ];


    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendSchoolEventConfirmationTemplate(User $user, $data = []): mixed
  {

    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Type: {$data['type']}\n"
            . "Title: {$data['title']}\n"
            . "Description: {$data['description']}\n\n"
            . "Start Date: {$data['start_date']}\n"
            . "End Date: {$data['end_date']}\n\n"
            . "Please confirm if you want to add event."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_EVENT_ADD_CONFIRM,
                'title' => '✅ Confirm'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT,
                'title' => '✏️ Edit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_'.WhatsAppConstants::SCHOOL_EVENT_ADD_CANCEL,
                'title' => '❌ Cancel'
              ]
            ]
          ]
        ]
      ]
    ];


    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }


  public function sendRestaurantModuleButtons(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Hello, I’m Citio, your friendly restaurant assistant chatbot 🤖✨.\n"
            . "Now you can Add Offers, and announcements directly to your customers 📢.\n"
            . "Take full advantage of all Citio services to connect, engage, and communicate instantly 🤝✨.\n\n"
            . "Just select an option below to start using all the features of Citio!"
        ],
        'action' => [
          'button' => 'Select an Action', // The main button text
          'sections' => [
            [
              'title' => 'Quick Actions',
              'rows' => [
                [
                  'id' => '_restaurant_add-offer',
                  'title' => '➕ Add Offers',
                  'description' => 'Create and share special offers'
                ],
                [
                  'id' => '_restaurant_add-announcement',
                  'title' => '➕ Add Announcement',
                  'description' => 'Create and share important announcements'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendRestaurantModuleAddMoreButtons(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Ready to make another update? You can easily add more Offers, or Announcements to keep your restaurant customers informed and engaged. 📢✨\n\n"
            . "Just select an option below to continue using all the features of Citio!"
        ],
        'action' => [
          'button' => 'Select an Action', // The main button text
          'sections' => [
            [
              'title' => 'Quick Actions',
              'rows' => [
                [
                  'id' => '_restaurant_add-offer',
                  'title' => '➕ Add Offers',
                  'description' => 'Create and share special offers'
                ],
                [
                  'id' => '_restaurant_add-announcement',
                  'title' => '➕ Add Announcement',
                  'description' => 'Create and share important announcements'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  public function sendRestaurantConfirmationTemplate(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "🎉 Welcome to Citio, {$user->name} 🎉\n\n"
            . "Hello, I’m Citio, your friendly school assistant chatbot 🤖✨.\n"
            ."✅ You have been successfully approved! ✅\n\n"
            . "Now you can Add Sos Alert, Events, and announcements directly to your students 📢.\n"
            . "Take full advantage of all Citio services to connect, engage, and communicate instantly 🤝✨.\n\n"
            . "Just select an option below to start using all the features of Citio!"
        ],
        'action' => [
          'button' => 'Select an Action', // The main button text
          'sections' => [
            [
              'title' => 'Quick Actions',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Add New SOS Alert',
                  'description' => 'Send an urgent alert to students and staff'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Add New Announcement',
                  'description' => 'Create and share important announcements'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Add New Event',
                  'description' => 'Create and share upcoming events'
                ],
              ]
            ]
          ]
        ]
      ]
    ];


    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }
  public function sendRestaurantAnnouncementConfirmationTemplate(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Type: {$data['type']}\n"
            . "Title: {$data['title']}\n"
            . "Description: {$data['description']}\n\n"
            . "Start Date: {$data['start_date']}\n"
            . "End Date: {$data['end_date']}\n\n"
            . "Please confirm if you want to add announcement."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CONFIRM,
                'title' => '✅ Confirm'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT,
                'title' => '✏️ Edit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CANCEL,
                'title' => '❌ Cancel'
              ]
            ]
          ]
        ]
      ]
    ];


    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }


  public function sendRestaurantOfferConfirmationTemplate(User $user, $data = []): mixed
  {

    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Title: {$data['title']}\n"
            . "Description: {$data['description']}\n\n"
            . "Start Date: {$data['start_date']}\n"
            . "End Date: {$data['end_date']}\n\n"
            . "Discount: {$data['discount']} ({$data['discount_type']})\n\n"
            . "Applicable Items: " . (isset($data['applicable_items']) && is_array($data['applicable_items'])
              ? implode(', ', $data['applicable_items'])
              : ($data['applicable_items'] ?? '')
            ) . "\n\n"
            . "Please confirm if you want to add offer."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_OFFER_ADD_CONFIRM,
                'title' => '✅ Confirm'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT,
                'title' => '✏️ Edit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_'.WhatsAppConstants::RESTAURANT_OFFER_ADD_CANCEL,
                'title' => '❌ Cancel'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }
  /**
   * Send restaurant offer details to customer (English)
   */
  // public function sendRestaurantOffersDetailsToCustomer($mobileNumber, array $offerData = []): mixed
  // {
  //   $message = "🎉 " . ($offerData['restaurant_name'] ?? '') . " 🎉\n\n"
  //     . "🌟 " . ($offerData['title'] ?? '') . " 🌟\n\n"
  //     . "🔥 " . ($offerData['discount'] ?? '') . ($offerData['discount_type'] ?? '') . " OFF on your favorite meals! 🔥\n\n"
  //     . ($offerData['description'] ?? '') . "\n\n"
  //     . "✨ What's included: " . (isset($offerData['applicable_items']) && is_array($offerData['applicable_items'])
  //       ? implode(', ', $offerData['applicable_items'])
  //       : ($offerData['applicable_items'] ?? '')
  //     ) . "\n\n"
  //     . "⏰ Limited Time Only! Available from " . ($offerData['valid_from'] ?? '') . " until " . ($offerData['valid_until'] ?? '') . "\n\n"
  //     . "🚀 Don't wait! This mouth-watering deal won't last forever. Order now and save big on delicious food that'll make your taste buds dance!\n\n"
  //     . "👆 Tap to order now and claim your discount! 🛒✨\n\n"
  //     . ($offerData['restaurant_name'] ?? '') . " - where great food meets great savings! 🍽️💫";

  //   $data = [
  //     'messaging_product' => 'whatsapp',
  //     'to' => $mobileNumber,
  //     'type' => 'interactive',
  //     'recipient_type' => 'individual',
  //     'interactive' => [
  //       'type' => 'button',
  //       'body' => [
  //         'text' => $message
  //       ],
  //       'action' => [
  //         'buttons' => [
  //           [
  //             'type' => 'reply',
  //             'reply' => [
  //               'id' => ($offerData['restaurant_id'] ?? '') . '_restaurant_menu',
  //               'title' => '🛒 Order Now'
  //             ]
  //           ]
  //         ]
  //       ]
  //     ]
  //   ];

  //   return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  // }

  public function sendRestaurantOffersDetailsToCustomer($mobileNumber, array $offerData = [], string $languageCode = 'en'): mixed
  {
      $templateName = 'restaurant_offers_interactive';

      $parameters = [
        $offerData['restaurant_name'] ?? '',    // {{1}}
        $offerData['title'] ?? '',              // {{2}}
        $offerData['discount'] ?? '',           // {{3}}
        $offerData['discount_type'] ?? '',      // {{4}}
        $offerData['description'] ?? '',        // {{5}}
        is_array($offerData['applicable_items'])
            ? implode(', ', $offerData['applicable_items'])
            : ($offerData['applicable_items'] ?? ''), // {{6}}
        $offerData['valid_from'] ?? '',         // {{7}}
        $offerData['valid_until'] ?? '',        // {{8}}
        $offerData['restaurant_name'] ?? ''     // {{9}} - Same as {{1}}
      ];

      $buttonPayload = ($offerData['restaurant_id'] ?? '') . '_restaurant_menu';

      $response = $this->sendTemplateWithQuickReplyButton(
          $mobileNumber,
          $templateName,
          $languageCode,
          $parameters,
          $buttonPayload
      );
      info('response from custom send quick button reply is ' , ['offer_quick' => $response]);
      return $response;
  }

  // Create new method for quick reply buttons
  public function sendTemplateWithQuickReplyButton(
      string $mobile,
      string $templateName,
      string $languageCode,
      array $parameters = [],
      string $buttonPayload = ''
  ): mixed {
      $data = [
          'messaging_product' => 'whatsapp',
          'to' => $mobile,
          'type' => 'template',
          'template' => [
              "name" => $templateName,
              "language" => ["code" => $languageCode]
          ]
      ];

      // Add body parameters if provided
      if (!empty($parameters)) {
          $data['template']['components'][] = [
              'type' => 'body',
              'parameters' => $this->formatTextParameters($parameters)
          ];
      }

      // Add quick reply button if payload provided
      if (!empty($buttonPayload)) {
        $data['template']['components'][] = [
          'type' => 'button',
          'sub_type' => 'quick_reply',
          'index' => '0',
          'parameters' => [
            [
              'type' => 'payload',
              'payload' => $buttonPayload
            ]
          ]
        ];
      }

      return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  // ==================== CZECH LANGUAGE METHODS ====================

  /**
   * Send restaurant information as interactive message (Czech)
   */
  public function sendRestaurantWhatsAppTemplateMessageCs(User $user, Restaurant $restaurant): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'header' => [
          'type' => 'image',
          'image' => [
            'link' => $restaurant->logo ?? asset('assets/img/restaurant_placeholder.png')
          ]
        ],
        'body' => [
          'text' => $this->formatRestaurantDetailsCs($restaurant)
        ],
        'footer' => [
          'text' => 'Vyberte možnost níže'
        ],
        'action' => [
          'buttons' => $this->buildRestaurantTemplateButtonCs($user, $restaurant)
        ]
      ]
    ];
    info("Sending restaurant WhatsApp template message (Czech)", ['data' => $data]);
    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant menu as interactive list (Czech)
   */
  public function sendRestaurantMenuWhatsAppTemplateMessageCs(User $user, Restaurant $restaurant): mixed
  {
    $sections = $this->buildMenuSectionsCs($restaurant);

    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'header' => [
          'type' => 'text',
          'text' => "🍽 {$restaurant->user->name} Menu"
        ],
        'body' => [
          'text' => "Klikněte na položku pro přidání do objednávky"
        ],
        'action' => [
          'button' => "Položky menu",
          'sections' => $sections
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send cart management buttons (Czech)
   */
  public function sendRestaurantMenuAddMoreInteractiveButtonsCs(User $user, Restaurant $restaurant, string $cart): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'header' => [
          'type' => 'text',
          'text' => "🍽 {$restaurant->user->name} Košík"
        ],
        'body' => ['text' => $cart],
        'action' => [
          'buttons' => $this->buildCartActionButtonsCs($restaurant->id)
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Build restaurant template buttons (Czech)
   */
  private function buildRestaurantTemplateButtonCs(User $user, Restaurant $restaurant): array
  {
    $cart = RestaurantCart::query()
      ->where('restaurant_id', $restaurant->id)
      ->where('user_id', $user->id)
      ->where('is_order_placed', false)
      ->count();

    $buttons = [
      [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_menu',
          'title' => '🍽 Zobrazit menu'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_tablebooking',
          'title' => '📅 Rezervovat stůl'
        ]
      ],
    ];

    if ($cart > 0) {
      $buttons[] = [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_viewcart',
          'title' => "🛒 Zobrazit košík ({$cart})"
        ]
      ];
    } else {
      $buttons[] = [
        'type' => 'reply',
        'reply' => [
          'id' => $restaurant->id . '_restaurant_location',
          'title' => '📍 Zobrazit umístění'
        ]
      ];
    }

    return $buttons;
  }

  /**
   * Format restaurant details for display (Czech)
   */
  private function formatRestaurantDetailsCs(Restaurant $restaurant): string
  {
    $details = [
      "*{$restaurant->user->name}*",
      "🍽 Typ: {$restaurant->categories->implode('name', ', ')}",
      "🌶️ Kuchyně: {$restaurant->cuisines->implode('name', ', ')}",
      "📞 Telefon: {$restaurant->mobile_number}",
      "📍 Adresa: {$restaurant->address}",
    ];

    // Add optional fields only if they exist
    if ($restaurant->sustainabilities) {
      $details[] = "🚹 Udržitelnost: \n{$restaurant->sustainabilities}";
    }

    if ($restaurant->accessibilities) {
      $details[] = "✅ Dostupnost: \n{$restaurant->accessibilities}";
    }

    if ($restaurant->hours) {
      $details[] = "⏱️ Otevírací doba: \n{$restaurant->hours}";
    }

    return implode("\n", $details);
  }

  /**
   * Build menu sections for interactive list (Czech)
   */
  private function buildMenuSectionsCs(Restaurant $restaurant): array
  {
    $itemsGroupedByCategory = $restaurant->items
      ->load('category')
      ->groupBy(fn($item) => $item->category->name ?? 'Ostatní');

    return $itemsGroupedByCategory->map(function ($items, $categoryName) {
      $rows = $items->map(function ($item) {
        $offer = $item->offers()->where('starts_from', '<=', now())
          ->where('ends_at', '>=', now())
          ->first();
        if ($offer && $offer->discount_type = 'percentage') {
          $item->price = $item->price - ($item->price * $offer->discount / 100);
          $item->description = "(Sleva: {$offer->discount}%)" . " " . $item->description;
        } elseif ($offer && $offer->discount_type == 'fixed') {
          if ($item->price < $offer->discount) {
            $item->price = 0; // Ensure price doesn't go negative
          } else {
            $item->price = $item->price - $offer->discount;
          }
          $item->description = "(Sleva: {$offer->discount})" . " " . $item->description;
        }
        return [
          'id' => "{$item->id}_menu_additem",
          'title' => Str::limit($item->name, 18, '') . " - {$item->price}",
          'description' => Str::limit(trim($item->description), 70, '..'),
        ];
      });

      $categoryName = WhatsAppConstants::RESTAURANT_MENU_CATEGORY_MAPPINGS[$categoryName] ?? $categoryName;

      return [
        'title' => Str::limit($categoryName, 24, ''),
        'rows' => $rows->toArray()
      ];
    })->values()->toArray();
  }

  /**
   * Build cart action buttons (Czech)
   */
  private function buildCartActionButtonsCs(string $restaurantId): array
  {
    return [
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_menu",
          'title' => '➕ Přidat více'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_checkout",
          'title' => '🛒 Pokladna'
        ]
      ],
      [
        'type' => 'reply',
        'reply' => [
          'id' => "{$restaurantId}_restaurant_clearcart",
          'title' => '🗑️ Vyčistit košík'
        ]
      ]
    ];
  }

  /**
   * Send school module buttons (Czech)
   */
  public function sendSchoolModuleButtonsCs(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Ahoj, jsem Citio, váš přátelský školní asistent chatbot 🤖✨.\n"
            . "Nyní můžete přidat SOS upozornění, události a oznámení přímo svým studentům 📢.\n"
            . "Využijte plně všech služeb Citio pro propojení, zapojení a okamžitou komunikaci 🤝✨.\n\n"
            . "Stačí vybrat možnost níže a začít používat všechny funkce Citio!"
        ],
        'action' => [
          'button' => 'Vybrat akci',
          'sections' => [
            [
              'title' => 'Rychlé akce',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Nové SOS upozornění',
                  'description' => 'Odeslat naléhavé upozornění studentům a zaměstnancům'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Nové oznámení',
                  'description' => 'Vytvořit a sdílet důležitá oznámení'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Nová událost',
                  'description' => 'Vytvořit a sdílet nadcházející události'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send school module add more buttons (Czech)
   */
  public function sendSchoolModuleAddMoreButtonsCs(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Připraveni na další aktualizaci? Můžete snadno přidat více SOS upozornění, oznámení nebo událostí, abyste udrželi svou školní komunitu informovanou a zapojenou. 📢✨\n\n"
            . "Stačí vybrat možnost níže a pokračovat v používání všech funkcí Citio!"
        ],
        'action' => [
          'button' => 'Vybrat akci',
          'sections' => [
            [
              'title' => 'Rychlé akce',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Nové SOS upozornění',
                  'description' => 'Odeslat naléhavé upozornění studentům a zaměstnancům'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Nové oznámení',
                  'description' => 'Vytvořit a sdílet důležitá oznámení'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Nová událost',
                  'description' => 'Vytvořit a sdílet nadcházející události'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send school SOS confirmation template (Czech)
   */
  public function sendSchoolSosConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Typ: {$data['type']}\n"
            . "Nadpis: {$data['title']}\n"
            . "Zpráva: {$data['message']}\n\n"
            . "Prosím potvrďte, zda chcete přidat SOS upozornění."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_SOS_ADD_CONFIRM,
                'title' => '✅ Potvrdit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_SOS_ADD_EDIT,
                'title' => '✏️ Upravit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_SOS_ADD_CANCEL,
                'title' => '❌ Zrušit'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send school announcement confirmation template (Czech)
   */
  public function sendSchoolAnnouncementConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Typ: {$data['type']}\n"
            . "Nadpis: {$data['title']}\n"
            . "Popis: {$data['description']}\n\n"
            . "Datum začátku: {$data['start_date']}\n"
            . "Datum konce: {$data['end_date']}\n\n"
            . "Prosím potvrďte, zda chcete přidat oznámení."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CONFIRM,
                'title' => '✅ Potvrdit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_EDIT,
                'title' => '✏️ Upravit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_ANNOUNCEMENT_ADD_CANCEL,
                'title' => '❌ Zrušit'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send school event confirmation template (Czech)
   */
  public function sendSchoolEventConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Typ: {$data['type']}\n"
            . "Nadpis: {$data['title']}\n"
            . "Popis: {$data['description']}\n\n"
            . "Datum začátku: {$data['start_date']}\n"
            . "Datum konce: {$data['end_date']}\n\n"
            . "Prosím potvrďte, zda chcete přidat událost."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_EVENT_ADD_CONFIRM,
                'title' => '✅ Potvrdit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_EVENT_ADD_EDIT,
                'title' => '✏️ Upravit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_school_' . WhatsAppConstants::SCHOOL_EVENT_ADD_CANCEL,
                'title' => '❌ Zrušit'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant module buttons (Czech)
   */
  public function sendRestaurantModuleButtonsCs(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Ahoj, jsem Citio, váš přátelský restaurační asistent chatbot 🤖✨.\n"
            . "Nyní můžete přidat nabídky a oznámení přímo svým zákazníkům 📢.\n"
            . "Využijte plně všech služeb Citio pro propojení, zapojení a okamžitou komunikaci 🤝✨.\n\n"
            . "Stačí vybrat možnost níže a začít používat všechny funkce Citio!"
        ],
        'action' => [
          'button' => 'Vybrat akci',
          'sections' => [
            [
              'title' => 'Rychlé akce',
              'rows' => [
                [
                  'id' => '_restaurant_add-offer',
                  'title' => '➕ Přidat nabídky',
                  'description' => 'Vytvořit a sdílet speciální nabídky'
                ],
                [
                  'id' => '_restaurant_add-announcement',
                  'title' => '➕ Přidat oznámení',
                  'description' => 'Vytvořit a sdílet důležitá oznámení'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant module add more buttons (Czech)
   */
  public function sendRestaurantModuleAddMoreButtonsCs(User $user, School $school = null): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "Připraveni na další aktualizaci? Můžete snadno přidat více nabídek nebo oznámení, abyste udrželi své restaurační zákazníky informované a zapojené. 📢✨\n\n"
            . "Stačí vybrat možnost níže a pokračovat v používání všech funkcí Citio!"
        ],
        'action' => [
          'button' => 'Vybrat akci',
          'sections' => [
            [
              'title' => 'Rychlé akce',
              'rows' => [
                [
                  'id' => '_restaurant_add-offer',
                  'title' => '➕ Přidat nabídky',
                  'description' => 'Vytvořit a sdílet speciální nabídky'
                ],
                [
                  'id' => '_restaurant_add-announcement',
                  'title' => '➕ Přidat oznámení',
                  'description' => 'Vytvořit a sdílet důležitá oznámení'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant confirmation template (Czech)
   */
  public function sendRestaurantConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'list',
        'body' => [
          'text' => "🎉 Vítejte v Citio, {$user->name} 🎉\n\n"
            . "Ahoj, jsem Citio, váš přátelský školní asistent chatbot 🤖✨.\n"
            . "✅ Byli jste úspěšně schváleni! ✅\n\n"
            . "Nyní můžete přidat SOS upozornění, události a oznámení přímo svým studentům 📢.\n"
            . "Využijte plně všech služeb Citio pro propojení, zapojení a okamžitou komunikaci 🤝✨.\n\n"
            . "Stačí vybrat možnost níže a začít používat všechny funkce Citio!"
        ],
        'action' => [
          'button' => 'Vybrat akci',
          'sections' => [
            [
              'title' => 'Rychlé akce',
              'rows' => [
                [
                  'id' => '_school_add-sos-alert',
                  'title' => '➕ Přidat nové SOS upozornění',
                  'description' => 'Odeslat naléhavé upozornění studentům a zaměstnancům'
                ],
                [
                  'id' => '_school_add-announcement',
                  'title' => '➕ Přidat nové oznámení',
                  'description' => 'Vytvořit a sdílet důležitá oznámení'
                ],
                [
                  'id' => '_school_add-event',
                  'title' => '➕ Přidat novou událost',
                  'description' => 'Vytvořit a sdílet nadcházející události'
                ],
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }
  /**
   * Send restaurant offer details to customer (Czech)
   */
  public function sendRestaurantOffersDetailsToCustomerCs($mobileNumber, array $offerData = []): mixed
  {
    $message = "🎉 " . ($offerData['restaurant_name'] ?? '') . " 🎉\n\n"
      . "🌟 " . ($offerData['title'] ?? '') . " 🌟\n\n"
      . "🔥 " . ($offerData['discount'] ?? '') . ($offerData['discount_type'] ?? '') . " SLEVA na vaše oblíbené jídlo! 🔥\n\n"
      . ($offerData['description'] ?? '') . "\n\n"
      . "✨ Co je zahrnuto: "
      . (isset($offerData['applicable_items']) && is_array($offerData['applicable_items'])
          ? implode(', ', $offerData['applicable_items'])
          : ($offerData['applicable_items'] ?? '')
        ) . "\n\n"
      . "⏰ Pouze po omezenou dobu! Dostupné od " . ($offerData['valid_from'] ?? '') . " do " . ($offerData['valid_until'] ?? '') . "\n\n"
      . "🚀 Nečekejte! Tato lahodná nabídka nebude trvat věčně. Objednejte nyní a ušetřete na chutném jídle, které rozhýbe vaše chuťové buňky!\n\n"
      . "👆 Klepněte pro objednání a uplatněte slevu! 🛒✨\n\n"
      . ($offerData['restaurant_name'] ?? '') . " - kde se skvělé jídlo setkává se skvělými úsporami! 🍽️💫";

    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $mobileNumber,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => $message
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => ($offerData['restaurant_id'] ?? '') . '_restaurant_menu',
                'title' => '🛒 Objednat nyní'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant announcement confirmation template (Czech)
   */
  public function sendRestaurantAnnouncementConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Typ: {$data['type']}\n"
            . "Nadpis: {$data['title']}\n"
            . "Popis: {$data['description']}\n\n"
            . "Datum začátku: {$data['start_date']}\n"
            . "Datum konce: {$data['end_date']}\n\n"
            . "Prosím potvrďte, zda chcete přidat oznámení."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CONFIRM,
                'title' => '✅ Potvrdit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_EDIT,
                'title' => '✏️ Upravit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_ANNOUNCEMENT_ADD_CANCEL,
                'title' => '❌ Zrušit'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }

  /**
   * Send restaurant offer confirmation template (Czech)
   */
  public function sendRestaurantOfferConfirmationTemplateCs(User $user, $data = []): mixed
  {
    $data = [
      'messaging_product' => 'whatsapp',
      'to' => $user->mobile_number,
      'type' => 'interactive',
      'recipient_type' => 'individual',
      'interactive' => [
        'type' => 'button',
        'body' => [
          'text' => "Nadpis: {$data['title']}\n"
            . "Popis: {$data['description']}\n\n"
            . "Datum začátku: {$data['start_date']}\n"
            . "Datum konce: {$data['end_date']}\n\n"
            . "Sleva: {$data['discount']} ({$data['discount_type']})\n\n"
            . "Platné položky: " . (isset($data['applicable_items']) && is_array($data['applicable_items'])
              ? implode(', ', $data['applicable_items'])
              : ($data['applicable_items'] ?? '')
            ) . "\n\n"
            . "Prosím potvrďte, zda chcete přidat nabídku."
        ],
        'action' => [
          'buttons' => [
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_OFFER_ADD_CONFIRM,
                'title' => '✅ Potvrdit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_OFFER_ADD_EDIT,
                'title' => '✏️ Upravit'
              ]
            ],
            [
              'type' => 'reply',
              'reply' => [
                'id' => $data['chat_id'] . '_restaurant_' . WhatsAppConstants::RESTAURANT_OFFER_ADD_CANCEL,
                'title' => '❌ Zrušit'
              ]
            ]
          ]
        ]
      ]
    ];

    return $this->callApi(config('constant.360dialog.paths.messages'), $data);
  }
}
