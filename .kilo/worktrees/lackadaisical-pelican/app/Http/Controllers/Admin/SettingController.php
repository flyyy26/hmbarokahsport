<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::first();
        return view('admin.settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['nullable', 'string', 'max:255'],
            'store_description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'google_maps' => ['nullable', 'string', 'max:5000'],
            'instagram' => ['nullable', 'string', 'max:500'],
            'facebook' => ['nullable', 'string', 'max:500'],
            'tiktok' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = Setting::first();
        $oldLogo = $setting?->logo;
        $oldFavicon = $setting?->favicon;
        $newLogo = null;
        $newFavicon = null;

        try {
            if ($request->hasFile('logo')) {
                $newLogo = $request->file('logo')->store('settings', 'public');
            }

            if ($request->hasFile('favicon')) {
                $newFavicon = $request->file('favicon')->store('settings', 'public');
            }

            DB::transaction(function () use ($setting, $validated, $newLogo, $newFavicon) {
                if (!$setting) {
                    Setting::create([
                        'store_name' => $validated['store_name'] ?? null,
                        'store_description' => $validated['store_description'] ?? null,
                        'logo' => $newLogo,
                        'favicon' => $newFavicon,
                        'phone' => $validated['phone'] ?? null,
                        'whatsapp' => $validated['whatsapp'] ?? null,
                        'email' => $validated['email'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'postal_code' => $validated['postal_code'] ?? null,
                        'google_maps' => $validated['google_maps'] ?? null,
                        'instagram' => $validated['instagram'] ?? null,
                        'facebook' => $validated['facebook'] ?? null,
                        'tiktok' => $validated['tiktok'] ?? null,
                    ]);
                } else {
                    $setting->update([
                        'store_name' => $validated['store_name'] ?? null,
                        'store_description' => $validated['store_description'] ?? null,
                        'phone' => $validated['phone'] ?? null,
                        'whatsapp' => $validated['whatsapp'] ?? null,
                        'email' => $validated['email'] ?? null,
                        'address' => $validated['address'] ?? null,
                        'postal_code' => $validated['postal_code'] ?? null,
                        'google_maps' => $validated['google_maps'] ?? null,
                        'instagram' => $validated['instagram'] ?? null,
                        'facebook' => $validated['facebook'] ?? null,
                        'tiktok' => $validated['tiktok'] ?? null,
                        ...($newLogo ? ['logo' => $newLogo] : []),
                        ...($newFavicon ? ['favicon' => $newFavicon] : []),
                    ]);
                }
            });

            // Hapus file lama jika ada
            if ($newLogo && $oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            if ($newFavicon && $oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            return redirect()
                ->route('admin.settings.edit')
                ->with('success', 'Pengaturan toko berhasil disimpan.');

        } catch (Throwable $e) {
            // Hapus file baru jika gagal
            if ($newLogo && Storage::disk('public')->exists($newLogo)) {
                Storage::disk('public')->delete($newLogo);
            }
            if ($newFavicon && Storage::disk('public')->exists($newFavicon)) {
                Storage::disk('public')->delete($newFavicon);
            }

            report($e);
            return back()
                ->withInput()
                ->with('error', 'Pengaturan toko gagal disimpan: ' . $e->getMessage());
        }
    }
}