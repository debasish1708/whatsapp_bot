<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyIfRestaurantProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user()->role->slug!='restaurant'){
            return redirect()->route('home');
        }
        $restaurant = Auth::user()->restaurant;

        if(!$restaurant->is_profile_completed){
            return redirect()->route('home')->with('warning', 'Please setup your profile first.');
        }
        return $next($request);
    }
}
