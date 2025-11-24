<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('test',function (){
  $data = [
    'school_name' => 'Greenwood High',
  ];
  $user = \App\Models\User::find('9f2c80eb-d044-456e-a985-f913d6feec38');
  (new \App\Dialog360\Dialog360())->sendSchoolModuleButtons($user);
});
