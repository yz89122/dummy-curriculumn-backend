<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Log;

class LogResponseBody
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
        Log::info('RESPONSE_BODY: '.$response->getContent());
        return $response;
    }
}
