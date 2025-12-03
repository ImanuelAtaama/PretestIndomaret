<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // arahkan ke login kalau belum login
        }

        if (Auth::user()->id_role == $role) {
            return $next($request); // role cocok, lanjut
        }

        // jika role tidak cocok, logout dan redirect ke login
        Auth::logout();

        return redirect()->route('login')->with('error', 'Role Anda telah berubah, silahkan login kembali.');
    }
}
