<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTableReservation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];

    protected $casts = [
        'booking_date' => 'date',
    ];
    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function tableHour()
    {
        return $this->belongsTo(RestaurantTableHour::class, 'restaurant_table_hour_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
