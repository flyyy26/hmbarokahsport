<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account');
        }
        
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('customer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $isAjax = $request->ajax() || $request->wantsJson();

        if (Auth::guard('customer')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('customer')->user();

            // HAPUS SESSION CART & WISHLIST
            session()->forget('cart');
            session()->forget('wishlist');
            session()->forget('is_buy_now');
            session()->forget('old_cart_backup');

            if ($isAjax) {
                // 🔥 GENERATE CSRF TOKEN BARU
                $csrfToken = csrf_token();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'csrf_token' => $csrfToken,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]);
            }

            return redirect()
                ->route('customer.account')
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        if ($isAjax) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', 'Email atau password salah.');
    }

    public function showRegister()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account');
        }

        return view('customer.auth.register');
    }

    public function register(Request $request)
    {
        // 🔥 CEK APAKAH REQUEST DARI AJAX
        $isAjax = $request->ajax() || $request->wantsJson();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
            ]);

            Auth::guard('customer')->login($user);

            if ($isAjax) {
                $csrfToken = csrf_token();
                return response()->json([
                    'success' => true,
                    'message' => 'Registrasi berhasil',
                    'csrf_token' => $csrfToken,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ]);
            }

            return redirect()
                ->route('customer.account')
                ->with('success', 'Selamat datang, ' . $user->name . '! Akun Anda berhasil dibuat.');

        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registrasi gagal: ' . $e->getMessage()
                ], 500);
            }
            return back()
                ->withInput()
                ->with('error', 'Registrasi gagal: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('customer.login')
            ->with('success', 'Berhasil keluar.');
    }
}