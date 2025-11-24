<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClub extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
     'meeting_time' => 'datetime',
    ];
    public function shool(){
        return $this->belongsTo(School::class);
    }

}
