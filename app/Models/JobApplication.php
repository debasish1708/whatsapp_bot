<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class JobApplication extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = ['id'];
    public function resume(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (strlen($value) == 0) {
                    return null;
                }
                return Storage::disk('s3')->url('public/job_application/resume/' . $value);
            }
        );
    }
    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }
}
