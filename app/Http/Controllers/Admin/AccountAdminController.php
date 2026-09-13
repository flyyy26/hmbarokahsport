<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AccountAdminController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.account.edit', compact('user'));
    }

    /**
     * Update email & nama
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'name.max'       => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required'       => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required'               => 'Password baru wajib diisi.',
            'password.confirmed'              => 'Konfirmasi password tidak cocok.',
            'password.min'                    => 'Password minimal 8 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * 🔥 Update avatar admin
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
                'dimensions:min_width=100,min_height=100',
            ],
        ], [
            'avatar.required'   => 'Pilih file gambar terlebih dahulu.',
            'avatar.image'      => 'File harus berupa gambar.',
            'avatar.mimes'      => 'Format yang didukung: JPG, JPEG, PNG, WEBP.',
            'avatar.max'        => 'Ukuran maksimal 2MB.',
            'avatar.dimensions' => 'Dimensi minimal 100x100 pixel.',
        ]);

        // 🔥 Hapus avatar lama jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // 🔥 Simpan avatar baru
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Avatar berhasil diperbarui.');
    }

    /**
     * 🔥 Hapus avatar (opsional)
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Avatar berhasil dihapus.');
    }
}