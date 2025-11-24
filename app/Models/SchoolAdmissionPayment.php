<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolAdmissionPayment extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function admission(){
        return $this->belongsTo(SchoolAdmission::class);
    }
}
