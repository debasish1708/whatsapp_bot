<?php

namespace App\Http\Controllers\AppConfig;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public static function getSystemPrompt($key){
        return AppConfig::where('key',$key)->first()->value;
    }
}
