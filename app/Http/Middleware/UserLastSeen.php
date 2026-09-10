<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // I-update ang last_seen ng kasalukuyang naka-login na user
            DB::table('users')
                ->where('user_id', Auth::id())
                ->update(['last_seen' => now()]);
        }

        return $next($request);
    }
}