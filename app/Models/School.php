<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class School extends Model
{
    use HasUuids, HasFactory, SoftDeletes, CascadeSoftDeletes;

    protected $guarded = ['id'];

    protected $cascadeDeletes = ['user', 'announcements', 'clubs', 'psychologicalSupports', 'sosAlerts', 'eventCalenders', 'admissions', 'jobOffers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logo(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (strlen($value) == 0) {
                    return null;
                }
                return Storage::disk('s3')->url('public/school/logo/' . $value);
            }
        );
    }
    // public function category(){
    //     return $this->belongsToMany(SchoolCategory::class, 'category_id');
    // }
    public function announcements()
    {
        // return $this->hasMany(SchoolAnnouncement::class);
        return $this->morphMany(Annoucement::class, 'businessable');
    }

    public function clubs(){
        return $this->hasMany(SchoolClub::class);
    }
    public function psychologicalSupports(){
        return $this->hasMany(SchoolPsychologicalSupport::class);
    }
    public function sosAlerts(){
        return $this->hasMany(SchoolSosAlert::class);
    }
    public function eventCalenders(){
        return $this->hasMany(SchoolEvent::class);
    }
    public function admissions(){
        return $this->hasMany(SchoolAdmission::class);
    }
    // public function paymentSettings(){
    //     return $this->hasOne(SchoolPaymentSetting::class);
    // }
    public function jobOffers(){
        // return $this->hasMany(SchoolJobOffer::class);
        return $this->morphMany(JobOffer::class, 'businessable');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'school_user', 'school_id', 'user_id')
            ->withTimestamps();
    }

    public function categories(){
      return $this->belongsToMany(SchoolCategory::class, 'school_category_school');
    }

    public function businessUsers(){
        return $this->morphMany(BusinessUser::class, 'businessable');
    }

    public function members()
    {
        // return $this->hasMany(SchoolMember::class, 'school_id');
        return $this->morphMany(BusinessMember::class, 'businessable');
    }
}
