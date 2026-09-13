<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetRequest;
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
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        $isAjax = $request->ajax() || $request->wantsJson();

        $user = User::where('phone', $request->phone)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            // 🔥 CEK APAKAH AKUN AKTIF
            if (!$user->is_active) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.'
                    ], 403);
                }

                return back()
                    ->withInput($request->only('phone', 'remember'))
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin untuk informasi lebih lanjut.');
            }

            Auth::guard('customer')->login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $user = Auth::guard('customer')->user();

            // HAPUS SESSION CART & WISHLIST
            session()->forget('cart');
            session()->forget('wishlist');
            session()->forget('is_buy_now');
            session()->forget('old_cart_backup');

            if ($isAjax) {
                $csrfToken = csrf_token();

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'csrf_token' => $csrfToken,
                    'user' => [
                        'name' => $user->name,
                        'phone' => $user->phone,
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
                'message' => 'Nomor HP atau password salah'
            ], 401);
        }

        return back()
            ->withInput($request->only('phone', 'remember'))
            ->with('error', 'Nomor HP atau password salah.');
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
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
        ], [
            // 🔥 Custom messages dalam Bahasa Indonesia
            'name.required'     => 'Nama wajib diisi.',
            'name.max'          => 'Nama maksimal 255 karakter.',

            'phone.required'    => 'Nomor HP wajib diisi.',
            'phone.max'         => 'Nomor HP maksimal 30 karakter.',
            'phone.unique'      => 'Nomor HP sudah terdaftar. Silakan gunakan nomor lain atau login.',

            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'=> 'Konfirmasi kata sandi tidak cocok.',

            'terms.required'    => 'Anda harus menyetujui syarat & ketentuan.',
            'terms.accepted'    => 'Anda harus menyetujui syarat & ketentuan.',
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
                'email' => null,
                'phone' => $request->phone,
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
                        'phone' => $user->phone,
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

    public function showForgotPassword()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.account');
        }

        return view('customer.auth.forgot-password');
    }

    public function submitForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:30',
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Normalisasi nomor HP (hapus spasi, strip, +62 → 0)
        $phone = $this->normalizePhone($request->phone);

        // Cari user
        $user = User::where('phone', $phone)->first();

        // Selalu tampilkan pesan sukses (security: jangan bocorin nomor terdaftar)
        // Tapi kalau gak terdaftar, tetap kasih halaman "tidak terdaftar" via flash
        if (!$user) {
            return redirect()
                ->route('customer.forgot-password.result')
                ->with('not_registered', true)
                ->with('phone', $phone);
        }

        // Cek apakah user sudah punya request pending
        if (PasswordResetRequest::hasPendingRequest($user->id)) {
            return redirect()
                ->route('customer.forgot-password.result')
                ->with('already_pending', true)
                ->with('phone', $phone);
        }

        // Cek apakah user aktif
        if (!$user->is_active) {
            return redirect()
                ->route('customer.forgot-password.result')
                ->with('inactive_account', true)
                ->with('phone', $phone);
        }

        // Buat request baru
        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'phone' => $phone,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('customer.forgot-password.result')
            ->with('request_created', true)
            ->with('phone', $phone)
            ->with('request_id', $resetRequest->id);
    }

    public function showForgotPasswordResult(Request $request)
    {
        return view('customer.auth.forgot-password-result');
    }

    public function showResetPassword(string $token)
    {
        $resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest) {
            return view('customer.auth.reset-password-invalid', [
                'reason' => 'Token tidak ditemukan atau tidak valid.',
            ]);
        }

        if (!$resetRequest->isTokenValid()) {
            // Update status expired
            $resetRequest->update(['status' => 'expired']);
            return view('customer.auth.reset-password-invalid', [
                'reason' => 'Token sudah kadaluarsa. Silakan ajukan permintaan baru.',
            ]);
        }

        return view('customer.auth.reset-password', [
            'token' => $token,
            'user' => $resetRequest->user,
        ]);
    }

    /**
     * Proses reset password
     */
    public function processResetPassword(Request $request, string $token)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $resetRequest = PasswordResetRequest::where('token', $token)
            ->where('status', 'approved')
            ->first();

        if (!$resetRequest || !$resetRequest->isTokenValid()) {
            return view('customer.auth.reset-password-invalid', [
                'reason' => 'Token tidak valid atau sudah kadaluarsa.',
            ]);
        }

        // Update password user
        $user = $resetRequest->user;
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Tandai request sudah dipakai
        $resetRequest->markAsUsed();

        // Logout semua session user (opsional, biar aman)
        // Auth::guard('customer')->logoutOtherDevices($request->password);

        return redirect()
            ->route('customer.login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
    }

    /**
     * Helper: normalisasi nomor HP
     */
    protected function normalizePhone(string $phone): string
    {
        // Hapus semua karakter kecuali angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Kalau mulai dengan 62, ubah ke 0
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        // Kalau mulai dengan 8, tambah 0
        if (str_starts_with($phone, '8')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

}