<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppConfig extends Model
{
  //
  use HasFactory, SoftDeletes, HasUuids;
  protected $guarded = [];

  public static function getValueByKey($key)
  {
    return static::where('key', $key)->firstOrFail()->value;
  }
}
