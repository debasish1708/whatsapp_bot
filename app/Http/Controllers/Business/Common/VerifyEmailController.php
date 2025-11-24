<?php

namespace App\Http\Controllers\Business\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email'=>'required|email'
        ]);
        try{
            $email = strtolower($request->email);
            $user=User::whereEmail($email)->first();
            if(!$user){
                return redirect()->route('business.login')->with('error', 'Email not Found Please Enter valid Email.');
            }
            if($user->verified_at){
                return redirect()->route('business.login')->with('info', 'Email already verified');
            }
            $user->update(['verified_at'=>Carbon::now()]);
            return redirect()->route('business.login')->with('success', 'Email verified successfully');

        } catch(\Throwable $th){
           return redirect()->route('business.login')->with('error', 'Something went wrong');
        }
    }
}
