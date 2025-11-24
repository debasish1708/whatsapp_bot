<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolAdmission extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function school(){
        return $this->belongsTo(School::class);
    }
    // public function payments(){
    //     return $this->hasMany(SchoolAdmissionPayment::class);
    // }

    public function payments(){
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
