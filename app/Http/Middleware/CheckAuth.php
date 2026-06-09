<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('user_id')) {
            return response()->json(['status' => 'error', 'message' => 'Belum login'], 401);
        }

        return $next($request);
    }
}
