<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckSingleDeviceLogin
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if ($user->role != 'admin') {
            // Check if the user is logged in
            if ($user) {
                $currentSessionId = Session::getId();
                // Check if the current session ID matches the stored session ID
                if ($user->session_id !== $currentSessionId) {
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'You are logged in from another device.');
                }
            }
        }

        return $next($request);
    }
}
