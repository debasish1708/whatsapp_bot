<?php

namespace App\Http\Middleware;

use App\Enums\BusinessStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictUntilAdminVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=auth()->user();
        if(!is_null($user->verified_at) && $user->status != BusinessStatus::APPROVED->value && !$request->is('dashboard')) {
            return redirect()->route($user->role->slug.'.dashboard')->with('modal', true);
        }
        return $next($request);
    }
}
