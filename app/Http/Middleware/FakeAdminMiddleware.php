<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FakeAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-Admin') !== 'true') {
            return response()->json(['message' => 'Unauthorized. Admins only.'], 403);
        }

        return $next($request);
    }
}
