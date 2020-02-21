<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class CheckRole
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $role
     * @return mixed
     * @throws AuthorizationException
     */
    public function handle($request, Closure $next, string $role)
    {
        /** @var User $user */
        $user = $request->user();

        // The administrator has access to everything
        if ($role === 'admin' && $user->role->name == 'Admin') {
            return $next($request);
        }

        throw new AuthorizationException('You no have access to this action');
    }

}
