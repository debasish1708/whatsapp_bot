<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolPsychologicalSupportOfficeHour extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];
    public function psychologicalSupport(){
        return $this->belongsTo(SchoolPsychologicalSupport::class);
    }
}
