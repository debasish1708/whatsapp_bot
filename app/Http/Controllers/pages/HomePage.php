<?php

namespace App\Http\Controllers\pages;

use App\Enums\BusinessStatus;
use App\Http\Controllers\Controller;
use App\Models\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomePage extends Controller
{
  public function index()
  {
    $user = auth()->user();
    $school = $user->school;

    $visited_pages = collect(json_decode($school->visited_pages));

    $is_already_visited = $visited_pages->contains('home');

    if(!$is_already_visited && $user->status === BusinessStatus::APPROVED->value){
      $visited_pages->add('home');
      $school->update([
        'visited_pages'=>json_encode($visited_pages)
      ]);
    } else {
      $is_already_visited = true;
    }

    $total_applications = $school->admissions()->whereIn('status', ['new', 'inprocess'])->count();
    $announcements_count = $school->announcements()->whereDate('start_date', '>=', Carbon::now())->count();
    $connected_users_count = $school->businessUsers()->count();
    $clubs_count = $school->clubs()->count();

    $total_users = $school->businessUsers()->count();
    $admissions_count = $school->admissions()->count();
    $job_applications_count = \App\Models\JobApplication::whereIn('job_offer_id', $school->jobOffers()->pluck('id'))->count();
    $paid_admissions_count = $school->admissions()->where('payment_status', 'paid')->count();

    return view(
      'content.business.school.dashboard',
      compact('total_applications', 'announcements_count', 'connected_users_count',
                  'clubs_count','total_users', 'admissions_count', 'job_applications_count', 'paid_admissions_count', 'is_already_visited')
      );
  }

  public function restaurantDashboard()
  {
    $user = auth()->user();
    $restaurant = $user->restaurant;
    $visited_pages = collect(json_decode($restaurant->visited_pages));

    $is_already_visited = $visited_pages->contains('home');

    if(!$is_already_visited && $user->status === BusinessStatus::APPROVED->value){
      $visited_pages->add('home');
      $restaurant->update([
        'visited_pages'=>json_encode($visited_pages)
      ]);
    } else {
      $is_already_visited = true;
    }

    $connected_users_count = $restaurant->businessUsers()->count();
    $menu_items = $restaurant->items()->count();
    $announcements_count = $restaurant->announcements()->whereDate('start_date', '>=', Carbon::now())->count();
    $job_offers_count = $restaurant->jobOffers()->count();
    // dd($user);
    return view(
      'content.business.restaurants.dashboard',
      compact('connected_users_count', 'menu_items', 'announcements_count', 'job_offers_count','is_already_visited')
    );
  }

  public function adminDashboard()
  {
    $user = auth()->user();

    $total_users = User::whereHas('role', function ($query) {
      $query->where('slug', 'user');
    })->count();

    $businesses_count = User::whereHas('role', function ($query) {
      $query->where('slug', '<>', 'user')->where('slug', '<>', 'admin');
    })->where('status' ,'approved')->count();

    // $pending_reviews_count = User::whereHas('role', function ($query) {
    //   $query->where('slug', '<>', 'user')->where('slug', '<>', 'admin');
    // })->where('status', 'pending')->count();
    $pending_reviews_count = User::whereHas('role', function ($query) {
        $query->whereNotIn('slug', ['admin', 'user']);
    })->where('status', 'pending')->count();

    $subscriptions_count = Subscription::where('type', 'business')->count();

    // dd($total_users, $businesses_count, $pending_reviews_count, $subscriptions);

    return view(
      'content.admin.dashboard',
      compact('total_users', 'businesses_count', 'pending_reviews_count', 'subscriptions_count')
    );
  }
}
