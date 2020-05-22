<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\PermissionException;

class StudentOnly
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
        throw_unless(
            $request->user()->isStudent(),
            PermissionException::class,
            'You must be a Student to perform this action'
        );

        return $next($request);
    }
}
