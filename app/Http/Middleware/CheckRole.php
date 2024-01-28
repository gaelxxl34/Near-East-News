<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (session('user_role') !== $role) {
            // Redirect to a default page if the user doesn't have the required role
            return redirect('login');
        }

        return $next($request);
    }
}