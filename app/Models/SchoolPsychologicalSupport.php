<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolPsychologicalSupport extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function school(){
        return $this->belongsTo(School::class);
    }
    public function officeHours(){
        return $this->hasMany(SchoolPsychologicalSupportOfficeHour::class);
    }
}
