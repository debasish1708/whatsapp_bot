<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantOrder extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = [];

    public function cart(){
        return $this->hasMany(RestaurantCart::class, 'restaurant_order_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
