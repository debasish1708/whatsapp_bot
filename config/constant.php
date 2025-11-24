<?php
return [

  "360dialog" => [
    "base_url" => env("360DIALOG_BASE_URL"),
    "api_key" => env("360DIALOG_API_KEY"),
    "webhook_token" => env("360DIALOG_WEBHOOK_TOKEN"),
    "paths" => [
      "messages" => "/messages",
      "media" => "/media",
      "attachments"=>"/whatsapp_business/attachments/"
    ]
  ],
  "open_ai" => [
    "base_url" => env("OPEN_AI_BASE_URL"),
    "api_key" => env("OPEN_AI_API_KEY"),
    "model" => 'gpt-4.1-nano',
    "whisper_model" => 'gpt-4o-transcribe',
    "paths" => [
      "completions" => "/v1/chat/completions",
      "model_response" => "/v1/responses",
      "whisper" => "/v1/audio/transcriptions",
    ]
  ],
  "google_places" => [
    "api_key" => env("GOOGLE_PLACES_API_KEY"),
    "paths" => [
      "place_autocomplete" => "https://maps.googleapis.com/maps/api/place/autocomplete/json",
      "v1_autocomplete" => "https://places.googleapis.com/v1/places:autocomplete",
      "v1_place" => "https://places.googleapis.com/v1/places/",
    ]
  ],
  "school_announcements"=>[
    "general"=>"general",
    "exam"=>"exam",
    "holiday"=>"holiday",
    "substitution"=>"substitution",
    "other"=>"other",
  ],
  "school_sos_alerts"=>[
    "emergency"=>"🆘 Emergency",
    "alert"=>"⚠️ Alert",
    "fire_dril"=>"🔥 Fire Drill",
    "intrusion"=>"🚨 Intrusion",
    "medical_emergency"=>"💉 Medical Emergency",
    "weather_alert"=>"🌩️ Weather Alert",
    "other"=>"📢 Other"
  ],
  "restaurant_announcements"=>[
    "offer"=>"Offer",
    "new_item"=>"New Item",
    "milestone_celebration"=>"Milestone Celebration",
    "weekend_offer"=>"Weekend Offer",
  ],
  "subscription_plans" => [
    "type" => "business",
    "price_id" => "price_1RdSkNCNP9TdbRK7PQvWd58G",
    // "price_id" => "price_1Ri7NsCNP9TdbRK7u5E1mtpc", //1 day plan
  ],
  "langchain" => [
    "base_url" => 'https://.asdf.asdf/app3/',
    "business_base_url" => 'https://asf.asdf.cool/app2/',
    "x-api-key" => 'asdfasdfAJNOIVSODIH'
  ],

  '360dialog_whatsapp_number' => env('360DIALOG_WHATSAPP_NUMBER', '+42023984738274'),
  'stripe_checkout_status' =>[
    'paid' => 'paid',
    'unpaid' => 'unpaid',
  ],
  'restaurant_sustainabilities'=>[
    'we_offer_food_just_before_the_expiration_date'=>'We offer food just before the expiration date',
    'we_use_returnable_or_compostable_packaging'=>'We use returnable or compostable packaging',
    'we_cooperate_with_food_banks'=>'We cooperate with food banks'
  ],
  'restaurant_accessibilities_basic'=>[
    'wheelchair_access'=>'Wheelchair access',
    'free_wi_fi'=>'Free Wi-Fi',
    'mobile_phone_charger'=>'Mobile phone charger',
    'pet_friendly'=>'Pet-friendly (dogs allowed)',
  ],
  'restaurant_accessibilities_senior_friendly'=>[
    'quiet_zone_or_separate_space'=>'Quiet zone or separate space',
    'highlighted_and_easy_to_read_menu'=>'Highlighted and easy-to-read menu',
    'possibility_of_personal_assistance_when_ordering'=>'Possibility of personal assistance when ordering',
    'special_prices_or_discounts_65+'=>'Special prices / discounts 65+',
  ],
  'restaurant_accessibilities_student_friendly'=>[
    'student_menu_at_a_discounted_price'=>'Student menu at a discounted price',
    'possibility_of_payment_by_isic_card'=>'Possibility of payment by ISIC card',
    'quick_meals_in_under_5_minutes'=>'Quick meals in under 5 minutes',
    'quiet_study_corner_or_laptop_sockets'=>'Quiet study corner / laptop sockets',
  ],
  'restaurant_accessibilities_child_friendly'=>[
    'children_menu'=>'Children\'s menu',
    'high_chairs_or_changing_table'=>'High chairs / changing table',
    'play_corner'=>'Play corner',
    'possibility_of_heating_baby_food'=>'Possibility of heating baby food',
    'coloring_books_or_interactive_entertainment'=>'Coloring books or interactive entertainment',
  ],
  'whatsapp_templates' => [
    'new_user' => [
      'language' => 'en',
      'body' => "👋 Hi! I'm Citio  cool– your city assistant for residents and visitors.
        I can help you with:

        • Food and drink 🍽️
        • Housing 🏠
        • Schools and studies 🧑‍🏫
        • Hotels and accommodation 🏨
        • Work opportunities 👩‍💼
        • City info (events, reports, news) ℹ️

        Please choose an option below to get started.",
    ],
    'school_announcement' => [
      'body' => "
        📢 {{1}} Announcement

        📌 Type: {{2}}
        📝 Title: {{3}}
        🗒️ Details: {{4}}
        📅 Start: {{5}}
        📅 End: {{6}}

        If you have any questions, feel free to contact us.",
    ],
    'alert_notification' => [
      'body' => "
      🚨 {{1}} Alert

      📌 Type: {{4}}
      📝 Title: {{2}}
      💬 Message: {{3}}

      Please take note and act accordingly."
    ],
    'admission_payment' => [
      'body' => "
      Hi {{1}},

      To complete your admission, please pay {{2}} your admission fee using the secure link below:

      Thank you!"
    ],
    'restaurant_table_booking' => [
      'body' => "
      Thank you for your interest in booking a table at  {{1}}.

      Please Fill out the form to confirm your reservation:"
    ],
    'restaurant_offers' => [
      'body' => "
        🎉 {{1}} 🎉
        🌟 {{2}} 🌟
        🔥 {{3}}{{4}} OFF on your favorite meals! 🔥
        {{5}}

        ✨ What's included: {{8}}

        ⏰ Limited Time Only! Available from {{6}} until {{7}}

        🚀 Don't wait! This mouth-watering deal won't last forever. Order now and save big on delicious food that'll make your taste buds dance!

        👆 Tap to order now and claim your discount! 🛒✨

        {{1}} - where great food meets great savings! 🍽️💫"
    ],
    'hi_school' => [
      'body' => [
          'text' => "Hello, I’m Citio, your friendly school assistant chatbot 🤖✨.\n"
            . "Now you can Add Sos Alert, Events, and announcements directly to your students 📢.\n"
            . "Take full advantage of all Citio services to connect, engage, and communicate instantly 🤝✨.\n\n"
            . "Just select an option below to start using all the features of Citio!",

          'text_cs' => "Ahoj, jsem Citio, váš přátelský školní asistent chatbot 🤖✨.\n"
            . "Nyní můžete přidat SOS upozornění, události a oznámení přímo svým studentům 📢.\n"
            . "Využijte plně všech služeb Citio pro propojení, zapojení a okamžitou komunikaci 🤝✨.\n\n"
            . "Stačí vybrat možnost níže a začít používat všechny funkce Citio!"
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
    ],
    'hi_restaurant' => [
      'body' => [
          'text' => "Hello, I’m Citio, your friendly restaurant assistant chatbot 🤖✨.\n"
            . "Now you can Add Offers, and announcements directly to your customers 📢.\n"
            . "Take full advantage of all Citio services to connect, engage, and communicate instantly 🤝✨.\n\n"
            . "Just select an option below to start using all the features of Citio!",

          'text_cs' => "Ahoj, jsem Citio, váš přátelský restaurační asistent chatbot 🤖✨.\n"
            . "Nyní můžete přidat nabídky a oznámení přímo svým zákazníkům 📢.\n"
            . "Využijte plně všech služeb Citio pro propojení, zapojení a okamžitou komunikaci 🤝✨.\n\n"
            . "Stačí vybrat možnost níže a začít používat všechny funkce Citio!"
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
    ],
  ],

];
