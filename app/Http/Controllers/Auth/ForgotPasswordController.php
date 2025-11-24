<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use MailerSend\Exceptions\MailerSendException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function showLinkRequestForm(){
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.common.forgot-password', ['pageConfigs' => $pageConfigs]);
    }

    public function showResetForm(){
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.common.reset-password', ['pageConfigs' => $pageConfigs]);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email'=>['required','email']
        ]);
        try{
            $email=strtolower($request->email);
            $user=User::whereEmail($email)->first();
            if (!$user) {
                return redirect()->back()->with('error', 'Email not Found Please Enter valid Email.');
            }
            Mail::to($user->email)->send(new ResetPassword($user));
            return redirect()->back()->with('success', 'Reset password link sent on your email id.');
        } catch(MailerSendException $e){
            info('MailerSendException Reset Password: ' . $e->getMessage());
            throw \Illuminate\Validation\ValidationException::withMessages([
              'general' => 'ResetPassword Link sending failed. Please try again later.',
            ]);
        } catch(\Throwable $th){
            return redirect()->back()->with('warning', 'Something went wrong!');
        }
    }

    public function reset(Request $request){
        $request->validate([
            'id' => ['required', 'string', Rule::exists('users', 'id')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        try{
            $user = User::findOrFail($request->id);
            $user->password = bcrypt($request->password);
            $user->save();
            info('Password reset successfully for user with ID: '.$user->id);
            return redirect()->route('business.login')->with('success', 'Password reset successfully');
        }catch(\Throwable $th){
            info('Unable to reset password', ['error' => $th->getMessage()]);
            return redirect()->route('business.login')->with('error', 'Unable to reset password');
        }
    }
}
