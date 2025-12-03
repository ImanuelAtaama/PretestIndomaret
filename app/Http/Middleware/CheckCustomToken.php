<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCustomToken
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user login dan token ada di session
        if (!Auth::check() || !session('custom_token')) {
            Auth::logout();
            return redirect('/login');
        }

        return $next($request);
    }
}
