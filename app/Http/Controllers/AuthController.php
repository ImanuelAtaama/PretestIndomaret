<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // Untuk generate random token
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Buat custom_token
        $request->session()->put('custom_token', Str::random(60));
        return redirect()->intended('/dashboard');
    }



    return back()->withErrors([
        'username' => 'Login gagal',
    ]);
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('custom_token'); // Hapus token dari session
        return redirect('/');
    }

    public function dashboard()
    {
        // Cek token valid dari session
        if (!session('custom_token')) {
            Auth::logout();
            return redirect('/');
        }

        $user = Auth::user();
        if ($user->id_role == 1) { // Admin
            return view('admin.dashboard');
        }
        return view('welcome'); // Untuk role lain
    }
}
