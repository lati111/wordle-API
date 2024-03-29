<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->route()->getPrefix() === 'api') {
            if ($request->isMethod('post')) {
                if (!$request->expectsJson()) {
                    return route('auth.fail.token.false');
                }
            } else {
                return route('auth.fail.token.none');
            }
        } else {
            return route('login.show');
        }

    }
}
