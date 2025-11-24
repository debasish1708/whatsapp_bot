<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolJobOffer extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
      'expiry_date' => 'datetime'
    ];
    public function school(){
        return $this->belongsTo(School::class);
    }
    public function isExpired(){
        return $this->expiry_date && Carbon::now()->greaterThan($this->expiry_date);
    }
}
