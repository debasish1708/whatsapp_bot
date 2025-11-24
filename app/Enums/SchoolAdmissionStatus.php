<?php

namespace App\Enums;

enum SchoolAdmissionStatus :  string
{
    case NEW = 'new';
    case INPROCESS = 'inprocess';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
}
