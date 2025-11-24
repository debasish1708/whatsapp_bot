<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Restaurant extends Model
{
    use HasFactory, HasUuids, SoftDeletes, CascadeSoftDeletes;

    protected $cascadeDeletes = ['user', 'timings', 'items', 'announcements', 'jobOffers'];

    protected $guarded = [];
    public function logo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (strlen($value) == 0) {
                    return null;
                }
                return Storage::disk('s3')->url('public/restaurants/logo/' . $value);
            }
        );
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cuisines(){
        return $this->belongsToMany(Cuisine::class, 'restaurant_cuisine');
    }

    public function timings(){
        return $this->hasMany(RestaurantTiming::class, 'restaurant_id');
    }

    public function menu_categories(){
        return $this->hasMany(RestaurantMenuCategory::class, 'restaurant_id');
    }

    public function items(){
        return $this->hasMany(RestaurantMenuItem::class, 'restaurant_id');
    }

    public function announcements(){
        // return $this->hasMany(RestaurantAnnouncement::class, 'restaurant_id');
        return $this->morphMany(Annoucement::class, 'businessable');
    }

    public function categories(){
        return $this->belongsToMany(RestaurantCategory::class, 'restaurant_category_restaurant');
    }

    public function jobOffers(){
        // return $this->hasMany(RestaurantJobOffer::class, 'restaurant_id');
        return $this->morphMany(JobOffer::class, 'businessable');
    }

    public function businessUsers(){
        return $this->morphMany(BusinessUser::class, 'businessable');
    }

    public function offers(){
        return $this->hasMany(RestaurantOffer::class, 'restaurant_id');
    }

    public function carts(){
        return $this->hasMany(RestaurantCart::class, 'restaurant_id');
    }

    public function orders(){
        return $this->hasMany(RestaurantOrder::class, 'restaurant_id');
    }

    public function tables()
    {
        return $this->hasMany(RestaurantTable::class);
    }

    public function tableHours()
    {
        return $this->hasMany(RestaurantTableHour::class, 'restaurant_id');
    }

    // Restaurant.php
    public function reservations()
    {
        return $this->hasManyThrough(
            RestaurantTableReservation::class,
            RestaurantTable::class,
            'restaurant_id',        // Foreign key on RestaurantTable
            'restaurant_table_id',  // Foreign key on Reservation
            'id',                   // Local key on Restaurant
            'id'                    // Local key on RestaurantTable
        );
    }

    public function sustainability()
    {
        return $this->belongsToMany(Sustainability::class, 'restaurant_sustainability');
    }

    public function accessibility()
    {
        return $this->belongsToMany(Accessibility::class, 'restaurant_accessibility');
    }

    public function members()
    {
        // return $this->hasMany(RestaurantMember::class, 'restaurant_id');
        return $this->morphMany(BusinessMember::class, 'businessable');
    }
}
