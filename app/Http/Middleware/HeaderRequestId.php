<?php

namespace App\Http\Middleware;

use Closure;

class HeaderRequestId
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
        $response = $next($request);
        $response->header('X-Request-ID', app('request_id'));
        return $response;
    }
}
