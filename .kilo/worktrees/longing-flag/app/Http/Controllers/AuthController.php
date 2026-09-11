<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // ✅ Jika sudah login, redirect ke dashboard
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // ✅ GUNakan guard 'web' untuk admin
        if (auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // ✅ CEK ROLE
            if (auth()->user()->role !== 'admin') {
                auth()->logout();
                return back()->with('error', 'Akun ini bukan admin.');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Admin!');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke route login admin ('admin.login' / 'login')
        return redirect()->route('login')->with('success', 'Berhasil keluar.');
    }
}