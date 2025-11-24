<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function show(){
        try {
            return view('content.common.change-password');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(ChangePasswordRequest $request){
        try {
            Auth::user()->update([
                'password'=>$request->newpass,
            ]);
            return redirect()->back()->with('success', __('Password updated successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
