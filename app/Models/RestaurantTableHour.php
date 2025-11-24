<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTableHour extends Model
{
  use SoftDeletes, HasUuids;

  protected $guarded = ["id"];



  public function restaurant()
  {
    return $this->belongsTo(Restaurant::class, 'restaurant_id');
  }
}


