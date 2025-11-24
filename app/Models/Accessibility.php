<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessibility extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = ['id'];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_accessibility');
    }
}
