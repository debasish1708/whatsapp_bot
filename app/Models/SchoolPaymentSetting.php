<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolPaymentSetting extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];
    public function school(){
        return $this->belongsTo(School::class);
    }
}
