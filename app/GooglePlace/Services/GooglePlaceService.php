<?php

namespace App\GooglePlace\Services;

use App\Constants\GooglePlaceConstants;
use App\GooglePlace\DataTransferObjects\GooglePlaceDTO;
use App\GooglePlace\Exceptions\GooglePlaceException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class GooglePlaceService
{
    private string $apiKey;
    private string $autoCompleteUrl;
    private string $placeDetailsUrl;

    public function __construct()
    {
        $this->apiKey = config('constant.google_places.api_key');
        $this->autoCompleteUrl = config('constant.google_places.paths.v1_autocomplete');
        $this->placeDetailsUrl = config('constant.google_places.paths.v1_place');
    }

    /**
     * Search for places using autocomplete
     */
    public function searchPlaces(string $query, array $options = []): array
    {
        if (empty(trim($query))) {
            throw new GooglePlaceException(GooglePlaceConstants::ERROR_MISSING_QUERY);
        }

        $requestData = $this->buildAutocompleteRequest($query, $options);

        try {
            $response = $this->makeHttpRequest('POST', $this->autoCompleteUrl, $requestData);
            return $response->json();
        } catch (\Exception $e) {
            throw new GooglePlaceException(GooglePlaceConstants::ERROR_API_CALL_FAILED . ': ' . $e->getMessage());
        }
    }

    /**
     * Get detailed information about a specific place
     */
    public function getPlaceDetails(string $placeId): GooglePlaceDTO
    {
        if (empty(trim($placeId))) {
            throw new GooglePlaceException(GooglePlaceConstants::ERROR_INVALID_PLACE_ID);
        }

        $url = $this->placeDetailsUrl . $placeId;

        try {
            $response = $this->makeHttpRequest('GET', $url);
            return new GooglePlaceDTO($response->json());
        } catch (\Exception $e) {
            throw new GooglePlaceException(GooglePlaceConstants::ERROR_API_CALL_FAILED . ': ' . $e->getMessage());
        }
    }

    /**
     * Build autocomplete request data
     */
    private function buildAutocompleteRequest(string $query, array $options = []): array
    {
        return [
            'input' => $query,
            'languageCode' => $options['languageCode'] ?? GooglePlaceConstants::DEFAULT_LANGUAGE_CODE,
            'regionCode' => $options['regionCode'] ?? GooglePlaceConstants::DEFAULT_REGION_CODE,
            'includedPrimaryTypes' => $options['includedPrimaryTypes'] ?? GooglePlaceConstants::SUPPORTED_PLACE_TYPES
        ];
    }

    /**
     * Make HTTP request to Google Places API
     */
    private function makeHttpRequest(string $method, string $url, array $data = []): Response
    {
        $headers = $this->getApiHeaders();

        if ($method === 'POST') {
            return Http::withHeaders($headers)->post($url, $data);
        }

        return Http::withHeaders($headers)->get($url);
    }

    /**
     * Get standard API headers
     */
    private function getApiHeaders(): array
    {
        return [
            'Content-Type' => GooglePlaceConstants::HEADER_CONTENT_TYPE,
            'X-Goog-FieldMask' => GooglePlaceConstants::HEADER_FIELD_MASK,
            'X-Goog-Api-Key' => $this->apiKey,
        ];
    }
}