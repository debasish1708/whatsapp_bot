<?php
namespace App\GooglePlace\DataTransferObjects;

class GooglePlaceDTO
{
    public ?string $id;
    public ?string $name;
    public ?string $mobileNumber;
    public ?string $address;
    public ?string $url;
    public ?string $type;
    public ?string $postalCode;
    public ?string $city;
    public ?string $country;
    public ?string $websiteUri;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $businessStatus;
    public ?bool $openNow;

    public function __construct(array $payload)
    {
        $this->id = $this->extractValue($payload, 'id');
        $this->name = $this->extractValue($payload, 'displayName.text');
        $this->mobileNumber = $this->extractValue($payload, 'internationalPhoneNumber');
        $this->address = $this->extractValue($payload, 'formattedAddress');
        $this->url = $this->extractValue($payload, 'googleMapsUri');
        $this->websiteUri = $this->extractValue($payload, 'websiteUri');
        $this->type = $this->extractValue($payload, 'primaryType');
        $this->businessStatus = $this->extractValue($payload, 'businessStatus');

        // Extract coordinates
        $this->latitude = $this->extractFloat($payload, 'location.latitude');
        $this->longitude = $this->extractFloat($payload, 'location.longitude');

        // Extract opening status
        $this->openNow = $this->extractBoolean($payload, 'currentOpeningHours.openNow')
            ?? $this->extractBoolean($payload, 'regularOpeningHours.openNow');

        // Extract postal address data
        $this->postalCode = $this->extractValue($payload, 'postalAddress.postalCode');
        $this->city = $this->extractValue($payload, 'postalAddress.locality');
        $this->country = $this->extractCountry($payload);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile_number' => $this->mobileNumber,
            'address' => $this->address,
            'url' => $this->url,
            'website_uri' => $this->websiteUri,
            'type' => $this->type,
            'postal_code' => $this->postalCode,
            'city' => $this->city,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'business_status' => $this->businessStatus,
            'open_now' => $this->openNow,
        ];
    }

    /**
     * Extract value from nested array using dot notation
     */
    private function extractValue(array $data, string $key): ?string
    {
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }

        return is_string($value) || is_numeric($value) ? (string)$value : null;
    }

    /**
     * Extract float value from nested array using dot notation
     */
    private function extractFloat(array $data, string $key): ?float
    {
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }

        return is_numeric($value) ? (float)$value : null;
    }

    /**
     * Extract boolean value from nested array using dot notation
     */
    private function extractBoolean(array $data, string $key): ?bool
    {
        $keys = explode('.', $key);
        $value = $data;

        foreach ($keys as $segment) {
            if (!is_array($value) || !isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }

        return is_bool($value) ? $value : null;
    }

    /**
     * Extract country from address components or postal address
     */
    private function extractCountry(array $payload): ?string
    {
        // First try to get from postalAddress (more reliable)
        $postalCountry = $this->getCountryFromPostalAddress($payload);
        if ($postalCountry) {
            return $postalCountry;
        }

        // Fallback to addressComponents
        return $this->getCountryFromAddressComponents($payload);
    }

    /**
     * Get country from postalAddress.regionCode and map it to full country name
     */
    private function getCountryFromPostalAddress(array $payload): ?string
    {
        $regionCode = $this->extractValue($payload, 'postalAddress.regionCode');

        if (!$regionCode) {
            return null;
        }

        // Map common country codes to full names
        $countryMap = [
            'CZ' => 'Czechia',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'CA' => 'Canada',
            'AU' => 'Australia',
            // Add more mappings as needed
        ];

        return $countryMap[$regionCode] ?? $regionCode;
    }

    /**
     * Extract country from address components
     */
    private function getCountryFromAddressComponents(array $payload): ?string
    {
        if (!isset($payload['addressComponents']) || !is_array($payload['addressComponents'])) {
            return null;
        }

        foreach ($payload['addressComponents'] as $component) {
            if (isset($component['types']) &&
                is_array($component['types']) &&
                in_array('country', $component['types'])) {
                return $component['longText'] ?? null;
            }
        }

        return null;
    }

    /**
     * Get formatted opening hours
     */
    public function getOpeningHours(): ?array
    {
        // This method could be added to extract opening hours if needed
        // Implementation would depend on your specific requirements
        return null;
    }

    /**
     * Check if the place has valid coordinates
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get the place types as an array
     */
    public function getTypes(): array
    {
        // This would need to be added to the constructor if you want to extract all types
        return [];
    }
}