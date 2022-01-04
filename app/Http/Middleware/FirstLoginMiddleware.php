<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class FirstLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();            

            if ($user->is_must_change_password == User::MUST_CHANGE_PASSWORD) {
                return redirect()->route("change_password");
            }
        }
        return $next($request);
    }
}
