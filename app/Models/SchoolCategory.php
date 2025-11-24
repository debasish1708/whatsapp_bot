<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolCategory extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    public function schools(){
        return $this->belongsToMany(School::class,'school_category_school');
    }
}
