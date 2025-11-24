<?php

namespace App\Enums;

enum SchoolEvents : string
{
    case Competition = 'competitions';
    case GuestLectures = 'guest_lectures';
    case OpenDays = 'open_days';
    case ClosingDays = 'closing_days';
    case Etc = 'etc';

    public function label() : string
    {
        return match($this) {
            self::Competition => 'danger',
            self::GuestLectures => 'primary',
            self::OpenDays => 'warning',
            self::ClosingDays => 'success',
            self::Etc => 'info',
        };
    }

    public function displayName() : string
    {
        return match($this) {
            self::Competition => __('Competitions'),
            self::GuestLectures => __('Guest Lectures'),
            self::OpenDays => __('Open Days'),
            self::ClosingDays => __('Closing Days'),
            self::Etc => __('Etc'),
        };
    }

    public function hexColor(): string
    {
        return match($this) {
            self::Competition    => '#ffc9c9',
            self::GuestLectures  => '#ffc1de',
            self::OpenDays       => '#ffe0b3',
            self::ClosingDays    => '#d4f8d4',
            self::Etc            => '#c8f5fa',
        };
    }
}
