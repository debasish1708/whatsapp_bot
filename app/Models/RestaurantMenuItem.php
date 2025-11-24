<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function category(){
        return $this->belongsTo(RestaurantMenuCategory::class, 'menu_category_id');
    }

    public function images(){
        return $this->hasMany(RestaurantMenuItemImages::class, 'menu_item_id');
    }

    public function offers(){
        return $this->belongsToMany(RestaurantOffer::class, 'offer_menu_item', 'menu_item_id', 'offer_id');
    }
}
