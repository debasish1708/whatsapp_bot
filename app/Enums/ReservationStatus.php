<?php

namespace App\Enums;

enum ReservationStatus : string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';

    public function badge(): array
    {
        return match($this) {
            self::PENDING   => ['label' => 'Pending', 'class' => 'bg-label-warning'],
            self::CONFIRMED => ['label' => 'Confirm', 'class' => 'bg-label-success'],
            self::REJECTED  => ['label' => 'Rejected', 'class' => 'bg-label-danger'],
            self::EXPIRED   => ['label' => 'Expired', 'class' => 'bg-label-danger'],
        };
    }

    public static function badgeFrom(string $value): array
    {
        return self::tryFrom($value)?->badge() ?? ['label' => ucfirst($value), 'class' => 'bg-label-secondary'];
    }
}
