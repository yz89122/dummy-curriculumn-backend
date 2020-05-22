<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\PermissionException;

class AdministratorOnly
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
            $request->user()->isAdministrator(),
            PermissionException::class,
            'You must be an Administrator to perform this action'
        );

        return $next($request);
    }
}
