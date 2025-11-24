<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuCategory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function restaurant(){
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function items(){
        return $this->hasMany(RestaurantMenuItem::class, 'menu_category_id');
    }
}
