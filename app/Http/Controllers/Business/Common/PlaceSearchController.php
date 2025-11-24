<?php

namespace App\Http\Controllers\Business\Common;

use App\Constants\GooglePlaceConstants;
use App\GooglePlace\Exceptions\GooglePlaceException;
use App\GooglePlace\Services\GooglePlaceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PlaceSearchController extends Controller
{
    private GooglePlaceService $googlePlaceService;

    public function __construct(GooglePlaceService $googlePlaceService)
    {
        $this->googlePlaceService = $googlePlaceService;
    }

    /**
     * Handle the incoming request for place autocomplete
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:1|max:255'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Invalid query parameter', 400);
            }

            $query = $request->query('query');

            $results = $this->googlePlaceService->searchPlaces($query);

            // Transform the response to match what the frontend expects
            $transformedResults = $this->transformApiResponse($results);

            return response()->json($transformedResults);

        } catch (GooglePlaceException $e) {
            Log::error('Google Places API Error: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage(), 400);

        } catch (\Exception $e) {
            Log::error('Unexpected error in place search: ' . $e->getMessage(), [
                'query' => $request->query('query'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    /**
     * Transform Google Places API response to match frontend expectations
     */
    private function transformApiResponse(array $results): array
    {
        // The Google Places API returns suggestions in the 'suggestions' key
        $suggestions = $results['suggestions'] ?? [];

        return [
            'status' => GooglePlaceConstants::STATUS_SUCCESS,
            'suggestions' => $suggestions,
            'count' => count($suggestions)
        ];
    }

    /**
     * Return error response
     */
    private function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'status' => GooglePlaceConstants::STATUS_ERROR,
            'message' => $message,
            'suggestions' => []
        ], $statusCode);
    }
}