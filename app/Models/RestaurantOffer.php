<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantOffer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function applicableItems(){
        return $this->belongsToMany(RestaurantMenuItem::class, 'offer_menu_item', 'offer_id', 'menu_item_id')
            ->withTimestamps();
    }
}
