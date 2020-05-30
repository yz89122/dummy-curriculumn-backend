<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\PermissionException;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        foreach ($roles as $role) {
            switch ($role) {
                case 'admin':
                case 'administrator':
                    if ($request->user()->isAdministrator()) {
                        return $next($request);
                    }
                break;
                case 'teacher':
                    if ($request->user()->isTeacher()) {
                        return $next($request);
                    }
                break;
                case 'student':
                    if ($request->user()->isStudent()) {
                        return $next($request);
                    }
                break;
            }
        }

        throw new PermissionException;
    }
}
