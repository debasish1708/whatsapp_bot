<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOffer extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function businessable()
    {
        return $this->morphTo();
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
