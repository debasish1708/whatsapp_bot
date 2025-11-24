<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      if(auth()->check() && auth()->user()->role->slug === 'admin'){
        return $next($request);
      }
      if (! $request->user() || ! $request->user()->subscribed('business')) {
          return redirect()->route('subscribe.form');
          // return $next($request);
      }

      return $next($request);

    }
}
