<?php

namespace App\Enums;

enum SchoolPsychologicalOfficeHour : string
{
    case Sunday =  'Sunday';
    case Monday = 'Monday';
    case Tuesday = 'Tuesday';
    case Wednesday = 'Wednesday';
    case Thursday = 'Thursday';
    case Friday = 'Friday';
    case Saturday = 'Saturday';

    public function dayColor(): string
    {
        return match($this) {
            self::Monday => 'bg-label-primary',
            self::Tuesday => 'bg-label-info',
            self::Wednesday => 'bg-label-success',
            self::Thursday => 'bg-label-warning',
            self::Friday => 'bg-label-dark',
            self::Saturday => 'bg-label-danger',
            self::Sunday => 'bg-label-warning',
        };
    }

    public function timeColor(): string
    {
        return match($this) {
            self::Monday => 'text-primary',
            self::Tuesday => 'text-info',
            self::Wednesday => 'text-success',
            self::Thursday => 'text-warning',
            self::Friday => 'text-dark',
            self::Saturday => 'text-danger',
            self::Sunday => 'text-warning',
        };
    }
}
