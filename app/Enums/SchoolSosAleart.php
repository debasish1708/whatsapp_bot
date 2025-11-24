<?php

namespace App\Enums;

enum SchoolSosAleart : string
{
    case Emergency = 'emergency';
    case Alert = 'alert';
    case FireDril = 'fire_dril';
    case Intrusion = 'intrusion';
    case MedicalEmergency = 'medical_emergency';
    case WeatherAlert = 'weather_alert';
    case Other = 'other';

    public function label() : string
    {
        return match($this) {
            self::Emergency => '🆘 Emergency',
            self::Alert => '⚠️ Alert',
            self::FireDril => '🔥 Fire Drill',
            self::Intrusion => '🚨 Intrusion',
            self::MedicalEmergency => '💉 Medical Emergency',
            self::WeatherAlert => '🌩️ Weather Alert',
            self::Other => '📢 Other',
        };
    }

    public static function safeFrom(?string $value): ?string
    {
        if (!$value) return null;

        $normalized = trim(mb_strtolower($value));

        $map = [
            // English
            'emergency' => '🆘 Emergency',
            'alert' => '⚠️ Alert',
            'fire drill' => '🔥 Fire Drill',
            'intrusion' => '🚨 Intrusion',
            'medical emergency' => '💉 Medical Emergency',
            'weather alert' => '🌩️ Weather Alert',
            'other' => '📢 Other',

            // Czech
            'nouzový stav' => '🆘 Nouzový stav',
            'upozornění' => '⚠️ Upozornění',
            'požární cvičení' => '🔥 Požární cvičení',
            'vniknutí' => '🚨 Vniknutí',
            'zdravotní nouze' => '💉 Zdravotní nouze',
            'varování před počasím' => '🌩️ Varování před počasím',
            'jiné' => '📢 Jiné',
        ];

        return $map[$normalized] ?? null;
    }


}
