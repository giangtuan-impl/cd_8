<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;

class CheckDeviceMiddleware
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
        $agent = new Agent();
        if($agent->isMobile()) {
            return response()->json([
                trans('messages.error') => trans('messages.device_not_support_page')
            ]);
        }
        return $next($request);
    }
}
