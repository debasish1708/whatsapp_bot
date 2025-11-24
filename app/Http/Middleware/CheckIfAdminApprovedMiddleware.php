<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAdminApprovedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if($user->status == 'pending'){
            Auth::logout();
            return redirect()->back()->with('warning', 'You are not approved by admin. Please wait until admin approves your account.');
        }

        if($user->status == 'rejected'){
            return redirect()
                ->route('restaurant.profile.edit')
                ->with('warning', 'Your profile is rejected by the admin. Please check and resubmit yor details.');
        }

        return $next($request);
    }
}
