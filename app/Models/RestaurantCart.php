<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantCart extends Model
{
    use SoftDeletes, HasUuids;

    protected $guarded = [];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function restaurantMenuItem()
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id');
    }

    public function order(){
        return $this->belongsTo(RestaurantOrder::class, 'restaurant_order_id');
    }

    public function restaurantOffer()
    {
        return $this->belongsTo(RestaurantOffer::class, 'restaurant_offer_id');
    }
}
