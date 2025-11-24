<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasUuids, Billable, CascadeSoftDeletes;

  protected $cascadeDeletes = ['businessUsers'];

  protected $guarded = ['id'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function role(){
    return $this->belongsTo(Role::class,'role_id');
  }

  public function whatsAppChats()
  {
    return $this->hasMany(WhatsAppChat::class);
  }

  public function school()
  {
    return $this->hasOne(School::class);
  }

  public function restaurant(){
    return $this->hasOne(Restaurant::class);
  }

  // public function schools()
  // {
  //   return $this->belongsToMany(School::class, 'school_user', 'user_id', 'school_id')
  //     ->withTimestamps();
  // }

  // public function schoolUser(){
  //   return $this->hasMany(SchoolUser::class, 'user_id');
  // }

  public function businessUsers(){
    return $this->hasMany(BusinessUser::class, 'user_id');
  }

  // public function restaurants()
  // {
  //     return $this->morphedByMany(Restaurant::class, 'businessable', 'business_user')
  //                 ->withPivot('added_by')
  //                 ->using(BusinessUser::class);
  // }

  // public function schools()
  // {
  //     return $this->morphedByMany(School::class, 'businessable', 'business_user')
  //                 ->withPivot('added_by')
  //                 ->using(BusinessUser::class);
  // }

  public function signup()
  {
    return $this->hasOne(UserSignup::class);
  }

  public function restaurantMember()
  {
    return $this->hasOne(RestaurantMember::class , 'user_id');
  }

  public function businessMembers()
  {
      return $this->hasOne(BusinessMember::class, 'user_id');
  }
}
