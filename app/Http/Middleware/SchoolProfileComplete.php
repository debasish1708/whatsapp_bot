<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!auth()->check()|| !in_array(auth()->user()->role->slug,['school','restaurant'])){
            return redirect()->route('home');
        }
        if(auth()->check()&&auth()->user()->role->slug=='restaurant'){
            $restaurant = auth()->user()->restaurant;
            if(!$restaurant->is_profile_completed){
                return redirect()->route('home')->with('warning', 'Please setup your profile first.');
            }
            return $next($request);
        }
        $school=auth()->user()->school;
        if(!$school->is_profile_completed) {
            // return redirect()->route('home')->with('school_profile',[
            //     'message' => 'Please setup your profile first.',
            //     'type' => 'warning'
            // ]);
            return redirect()->route('home')->with('warning', 'Please setup your profile first.');
        }
        return $next($request);
    }
}
