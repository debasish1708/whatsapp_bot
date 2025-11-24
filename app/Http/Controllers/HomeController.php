<?php

namespace App\Http\Controllers;

use App\GooglePlace\Services\GooglePlaceService;
use App\GooglePlace\Exceptions\GooglePlaceException;
use App\Models\Cuisine;
use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use App\Models\School;
use App\Models\SchoolCategory;
use App\Models\Sustainability;
use App\Models\Accessibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    private GooglePlaceService $googlePlaceService;

    public function __construct(GooglePlaceService $googlePlaceService)
    {
        $this->middleware('auth');
        $this->googlePlaceService = $googlePlaceService;
    }

    /**
     * Show the application dashboard
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $role = $user->role->slug;

            if (!$this->isUserVerified($user)) {
                return $this->handleUnverifiedUser();
            }

            // Handle incomplete profiles
            $profileResponse = $this->handleIncompleteProfile($user, $role);
            if ($profileResponse) {
                return $profileResponse;
            }

            $this->handleSubscriptionNotification();

            // 🔑 Business logic starts here
            if ($user->businessMembers()->exists()) {
                $businessMember = $user->businessMembers()->first();

                return match ($businessMember->businessable_type) {
                    Restaurant::class => redirect()->route('restaurant.dashboard'),
                    School::class => redirect()->route('school.dashboard'),
                    default => redirect()->route($role . '.dashboard'),
                };
            }

            return redirect()->route($role . '.dashboard');

        } catch (\Exception $e) {
            Log::error('Home controller error: ' . $e->getMessage());
            return redirect()->back()->with('response', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if user is verified
     */
    private function isUserVerified($user): bool
    {
        return !is_null($user->verified_at);
    }

    /**
     * Handle unverified user
     */
    private function handleUnverifiedUser()
    {
        Auth::logout();
        return redirect()->back()->with('error', 'Please verify your email address');
    }

    /**
     * Handle incomplete user profiles
     */
    private function handleIncompleteProfile($user, string $role)
    {
        switch ($role) {
            case 'school':
                return $this->handleIncompleteSchoolProfile($user);
            case 'restaurant':
                return $this->handleIncompleteRestaurantProfile($user);
            default:
                return null;
        }
    }

    /**
     * Handle incomplete school profile
     */
    private function handleIncompleteSchoolProfile($user)
    {
        if (!$user->school->is_profile_completed) {
            $schoolDetail = $user->school;
            $categories = SchoolCategory::all();
            $placeApiData = $this->getPlaceDetailsIfExists($schoolDetail->place_id);

            return view('content.business.school.profile.edit', compact(
                'user',
                'categories',
                'placeApiData',
                'schoolDetail'
            ));
        }
        return null;
    }

    /**
     * Handle incomplete restaurant profile
     */
    private function handleIncompleteRestaurantProfile($user)
    {
        $user->load(['restaurant.cuisines', 'restaurant.timings']);
        $restaurant = $user->restaurant;

        $sustainabilities = Sustainability::all();
        $accessibilities = Accessibility::all()->groupBy('category');

        if (!$restaurant->is_profile_completed) {
            $cuisines = Cuisine::all();
            $restaurant_categories = RestaurantCategory::all();
            $data = $this->getPlaceDetailsIfExists($restaurant->place_id);

            return view('content.business.restaurants.profile.edit', compact(
                'user',
                'cuisines',
                'data',
                'restaurant_categories',
                'sustainabilities',
                'accessibilities'
            ));
        }
        return null;
    }

    /**
     * Get place details if place ID exists
     */
    private function getPlaceDetailsIfExists(?string $placeId)
    {
        if (!$placeId) {
            return null;
        }

        try {
            return $this->googlePlaceService->getPlaceDetails($placeId);
        } catch (GooglePlaceException $e) {
            Log::error('Error fetching place details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle subscription expiration notification
     */
    private function handleSubscriptionNotification(): void
    {
        if (session()->exists('subscription')) {
            session()->flash('subscription', 'Your subscription is expiring soon. Please renew the plan');
        }
    }
}