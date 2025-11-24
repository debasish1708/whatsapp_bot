<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolEvent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
      'start_date' => 'datetime',
      'end_date' => 'datetime',
    ];
    public function school(){
        return $this->belongsTo(School::class);
    }
}
