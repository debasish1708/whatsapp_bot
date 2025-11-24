<?php

namespace App\Enums;

enum SchoolJobOffer : string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Expired = 'expired';
}
