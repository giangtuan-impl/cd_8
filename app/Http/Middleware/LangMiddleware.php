<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LangMiddleware
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
        $lang = $request->session()->get('lang');

        if ($lang) {
            app()->setlocale($lang);
        }

        if (Auth::check()) {
            $user = Auth::user();
            app()->setlocale($user->language);
        }
        
        return $next($request);
    }
}
