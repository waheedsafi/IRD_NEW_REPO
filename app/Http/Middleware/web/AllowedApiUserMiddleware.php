<?php

namespace App\Http\Middleware\web;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;

class AllowedApiUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()) {
            $role =  Auth::user()->role_id;

            if ($role == RoleEnum::debugger->value) {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        }
        return redirect()->route('web.login');
    }
}
