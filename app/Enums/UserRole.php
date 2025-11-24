<?php
namespace App\Enums;
enum UserRole: string{
    case School = 'school';
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case Museum = 'museum';
    case Admin = 'admin';

    public function values(): string{
        return array_column(self::cases(), 'value');
    }
}