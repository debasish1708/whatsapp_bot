<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsAppChat extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
