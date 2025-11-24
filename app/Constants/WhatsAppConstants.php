<?php

namespace App\Constants;

class WhatsAppConstants
{
  // Response Messages
  public const RESPONSE_SUCCESS = 'success';
  public const RESPONSE_DUPLICATE_WEBHOOK = 'Webhook already processed';
  public const RESPONSE_INVALID_TOKEN = 'Invalid webhook token';
  public const RESPONSE_INVALID_MESSAGING_PRODUCT = 'Invalid messaging product';
  public const RESPONSE_MISSING_DATA = 'Missing waId or message content';
  public const RESPONSE_UNSUPPORTED_MESSAGE_TYPE = 'Unsupported message type';
  public const RESPONSE_INTERNAL_ERROR = 'Internal Server Error';
  public const RESPONSE_RESTAURANT_NOT_FOUND = 'Restaurant not found.';
  public const RESPONSE_CART_EMPTY = '🛒 Your cart is empty.';
  public const RESPONSE_CART_CLEARED = '🛒 Your cart has been cleared.';
  public const RESPONSE_CART_CLEARED_CZ = '🛒 Váš košík byl vyprázdněn.';
  public const RESPONSE_UNDERSTANDING_ERROR = 'Sorry, I could not understand.';

  // Template Names
  public const TEMPLATE_NEW_USER = 'new_user';

  public const TYPE_RESTAURANT_MEMBER = 'restaurant_member';
  public const TYPE_SCHOOL_MEMBER = 'school_member';

  public const TEMPLATE_CITIO_POLICY = 'citio_guide';
  public const TEMPLATE_RESTAURANT_ORDER = 'restaurant_order';

  // Message Types
  public const MESSAGE_TYPE_TEXT = 'text';
  public const MESSAGE_TYPE_BUTTON = 'button';
  public const MESSAGE_TYPE_INTERACTIVE = 'interactive';
  public const MESSAGE_TYPE_LOCATION = 'location';
  public const MESSAGE_TYPE_AUDIO = 'audio';

  // Interactive Types
  public const INTERACTIVE_TYPE_BUTTON_REPLY = 'button_reply';
  public const INTERACTIVE_TYPE_LIST_REPLY = 'list_reply';

  // Button Actions
  public const ACTION_LOCATION = 'location';
  public const ACTION_MENU = 'menu';
  public const ACTION_CHECKOUT = 'checkout';
  public const ACTION_CLEAR_CART = 'clearcart';

  public const ACTION_VIEW_CART = 'viewcart';

  public const ACTION_TABLE_BOOKING = 'tablebooking';
  public const ACTION_ADD_ITEM = 'additem';
  public const ACTION_VIEW = 'view';

  // Button Types
  public const TYPE_RESTAURANT = 'restaurant';

  public const TYPE_SCHOOL = 'school';
  public const TYPE_MENU = 'menu';

  // Supported Message Types
  public const SUPPORTED_MESSAGE_TYPES = [
    self::MESSAGE_TYPE_TEXT,
    self::MESSAGE_TYPE_BUTTON,
    self::MESSAGE_TYPE_INTERACTIVE,
    self::MESSAGE_TYPE_LOCATION,
    self::MESSAGE_TYPE_AUDIO,
  ];

  // School Business Specific Constants
  public const SCHOOL_BUSINESS_ADD_NEW_SOS_ALERT =  'add-sos-alert';

  public const SCHOOL_SOS_ADD_CONFIRM = 'sos-confirm';

  public const SCHOOL_SOS_ADD_EDIT = 'sos-edit';
  public const SCHOOL_SOS_ADD_CANCEL = 'sos-cancel';
  public const TEMPLATE_SCHOOL_NEW_SOS_ALERT_PROTOTYPE = 'sos_alert_prototype';

  public const SCHOOL_BUSINESS_ADD_NEW_ANNOUNCEMENT = 'add-announcement';

  public const SCHOOL_ANNOUNCEMENT_ADD_CONFIRM = 'announcement-confirm';

  public const SCHOOL_ANNOUNCEMENT_ADD_CANCEL = 'announcement-cancel';

  public const SCHOOL_ANNOUNCEMENT_ADD_EDIT = 'announcement-edit';
  public const TEMPLATE_SCHOOL_NEW_ANNOUNCEMENT_PROTOTYPE = 'school_announcement_prototype';

  public const SCHOOL_BUSINESS_ADD_NEW_EVENT = 'add-event';
  public const SCHOOL_EVENT_ADD_CONFIRM = 'event-confirm';
  public const SCHOOL_EVENT_ADD_CANCEL = 'event-cancel';

  public const SCHOOL_EVENT_ADD_EDIT = 'event-edit';

  public const TEMPLATE_SCHOOL_NEW_EVENT_PROTOTYPE = 'school_event_prototype';


  // RESTAURANT BUSINESS SPECIFIC CONSTANTS

  public const RESTAURANT_BUSINESS_ADD_NEW_ANNOUNCEMENT = 'add-announcement';
  public const RESTAURANT_ANNOUNCEMENT_ADD_CONFIRM = 'announcement-confirm';

  public const RESTAURANT_ANNOUNCEMENT_ADD_EDIT = 'announcement-edit';

  public const RESTAURANT_ANNOUNCEMENT_ADD_CANCEL = 'announcement-cancel';

  public const TEMPLATE_RESTAURANT_NEW_ANNOUNCEMENT_PROTOTYPE = 'restaurant_announcement_prototype';

  public const RESTAURANT_BUSINESS_ADD_NEW_OFFER = 'add-offer';

  public const RESTAURANT_OFFER_ADD_CONFIRM = 'offer-confirm';
  public const RESTAURANT_OFFER_ADD_CANCEL = 'offer-cancel';

  public const RESTAURANT_OFFER_ADD_EDIT = 'offer-edit';

  public const TEMPLATE_RESTAURANT_NEW_OFFER_PROTOTYPE = 'restaurant_offer_prototype';

  // Greeting Messages
  public const GREETING_HELLO = 'hello';
  public const GREETING_HI = 'hi';
  public const GREETING_HI_CS = 'ahoj';

  // Button Text Mappings
  public const BUTTON_TEXT_MAPPINGS = [
    'Schools and studies' => 'Please send a list of schools.',
    'Food and drink' => 'Please send a list of restaurants.',
    'Školy a studia' => 'Prosím o zaslání seznamu škol.',
    'Jídlo a pití' => 'Prosím o zaslání seznamu restaurací.'
  ];

  public const RESTAURANT_MENU_CATEGORY_MAPPINGS = [
    'starter' => 'Předkrmy',
    'Main course' => 'Hlavní chod',
    'Dessert' => 'Dezerty',
  ];
  // Default Button Text
  public const DEFAULT_BUTTON_TEXT = 'Please send a list of schools.';

  // Webhook Vendor
  public const WEBHOOK_VENDOR = '360dialog';

  // Messaging Product
  public const MESSAGING_PRODUCT_WHATSAPP = 'whatsapp';

  // Language
  public const LANGUAGE_EN = 'en';

  public const LANGUAGE_CS = 'cs';

  // Role
  public const ROLE_USER = 'user';

  // File Extensions
  public const AUDIO_FILE_EXTENSION = '.ogg';

  // Weekday Order
  public const WEEKDAY_ORDER = [
    'monday' => 1,
    'tuesday' => 2,
    'wednesday' => 3,
    'thursday' => 4,
    'friday' => 5,
    'saturday' => 6,
    'sunday' => 7,
  ];

  // Currency
  public const CURRENCY_USD = 'usd';
  public const CURRENCY_CZK = 'CZK';
  public const CURRENCY_INR = '₹';

  // Stripe
  public const STRIPE_CENTS_MULTIPLIER = 100;

  // Media Storage
  public const MEDIA_STORAGE_PATH = 'app/media/';

  // Routes
  public const ROUTE_PAYMENT_SUCCESS = 'restaurant.admission.payment.success';
  public const ROUTE_PAYMENT_FAILED = 'restaurant.admission.payment.failed';

  // Metadata
  public const METADATA_TYPE_RESTAURANT_ORDER = 'restaurant_order_payment';
  public const METADATA_TYPE_SCHOOL_ADMISSION = 'school_admission_payment';
  public const METADATA_SUBSCRIPTION = 'subscription';

  // Business User Added By
  public const BUSINESS_USER_ADDED_BY_SEARCH = 'search';

  // Special Restaurant Details
  public const SPECIAL_RESTAURANT_TEXT = "Send a detail of restaurant Illeta";

}
