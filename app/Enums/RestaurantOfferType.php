<?php

namespace App\Enums;

enum RestaurantOfferType : string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label() : string
    {
        return match($this) {
            self::FIXED => 'Fixed',
            self::PERCENTAGE => 'Percentage'
        };
    }
}
