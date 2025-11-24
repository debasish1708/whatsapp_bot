<?php

namespace App\Constants;

class GooglePlaceConstants
{
    // API Configuration
    public const DEFAULT_LANGUAGE_CODE = 'cs';
    public const DEFAULT_REGION_CODE = 'CZ';

    // HTTP Headers
    public const HEADER_CONTENT_TYPE = 'application/json';
    public const HEADER_FIELD_MASK = '*';

    // Supported Place Types
    public const SUPPORTED_PLACE_TYPES = [
        'school',
        'restaurant',
        'hotel',
        'museum'
    ];

    // Error Messages
    public const ERROR_API_CALL_FAILED = 'Google Places API call failed';
    public const ERROR_INVALID_PLACE_ID = 'Invalid place ID provided';
    public const ERROR_MISSING_QUERY = 'Search query is required';

    // Response Status
    public const STATUS_SUCCESS = 'success';
    public const STATUS_ERROR = 'error';
}