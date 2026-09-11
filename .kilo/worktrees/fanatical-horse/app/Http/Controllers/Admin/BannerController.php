<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\PromoBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;

class BannerController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $banners = Banner::orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        $promoBar = PromoBar::first();

        return view('admin.banners.index', compact('banners', 'promoBar'));
    }

    public function updatePromoBar(Request $request)
    {
        $validated = $request->validate([
            'text_left' => ['nullable', 'string', 'max:500'],
            'text_right' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $promoBar = PromoBar::first();

        if ($promoBar) {
            $promoBar->update([
                'text_left' => $validated['text_left'] ?? null,
                'text_right' => $validated['text_right'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        } else {
            PromoBar::create([
                'text_left' => $validated['text_left'] ?? null,
                'text_right' => $validated['text_right'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        }

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Promo Bar berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.banners.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // 🔥 LOG untuk debug
        Log::info('=== STORE BANNER ===');
        Log::info('Request data:', $request->all());

        try {
            $validated = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'subtitle' => ['nullable', 'string', 'max:255'],
                'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'button_text' => ['nullable', 'string', 'max:100'],
                'button_url' => ['nullable', 'string', 'max:500'],
                'sort_order' => ['required', 'integer', 'min:0'],
                'is_active' => ['nullable', 'boolean'],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            ]);

            Log::info('Validated data:', $validated);

            $uploadedFiles = [];

            // Upload gambar desktop
            $imagePath = $request->file('image')->store('banners', 'public');
            $uploadedFiles[] = $imagePath;
            Log::info('Desktop image uploaded: ' . $imagePath);

            // Upload gambar mobile (opsional)
            $imageMobilePath = null;
            if ($request->hasFile('image_mobile') && $request->file('image_mobile')->isValid()) {
                $imageMobilePath = $request->file('image_mobile')->store('banners/mobile', 'public');
                $uploadedFiles[] = $imageMobilePath;
                Log::info('Mobile image uploaded: ' . $imageMobilePath);
            }

            // 🔥 PERBAIKI: Simpan data dengan benar
            $banner = Banner::create([
                'title' => $validated['title'] ?? null,
                'subtitle' => $validated['subtitle'] ?? null,
                'image' => $imagePath,
                'image_mobile' => $imageMobilePath,
                'button_text' => $validated['button_text'] ?? null,
                'button_url' => $validated['button_url'] ?? null,
                'sort_order' => (int) ($validated['sort_order'] ?? 0),
                'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : false,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
            ]);

            Log::info('Banner created successfully, ID: ' . $banner->id);

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal: ' . $e->getMessage());

        } catch (Throwable $e) {
            Log::error('Store banner error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus file yang sudah terupload
            if (isset($uploadedFiles)) {
                foreach ($uploadedFiles as $path) {
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                        Log::info('Deleted uploaded file: ' . $path);
                    }
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Banner gagal ditambahkan: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Banner $banner)
    {
        Log::info('=== UPDATE BANNER ===');
        Log::info('Banner ID: ' . $banner->id);

        try {
            $validated = $request->validate([
                'title' => ['nullable', 'string', 'max:255'],
                'subtitle' => ['nullable', 'string', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'button_text' => ['nullable', 'string', 'max:100'],
                'button_url' => ['nullable', 'string', 'max:500'],
                'sort_order' => ['required', 'integer', 'min:0'],
                'is_active' => ['nullable', 'boolean'],
                'starts_at' => ['nullable', 'date'],
                'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            ]);

            Log::info('Validated data:', $validated);

            $oldImage = $banner->image;
            $oldImageMobile = $banner->image_mobile;
            $newImagePath = null;
            $newImageMobilePath = null;

            // Upload gambar desktop baru
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $newImagePath = $request->file('image')->store('banners', 'public');
                Log::info('New desktop image: ' . $newImagePath);
            }

            // Upload gambar mobile baru
            if ($request->hasFile('image_mobile') && $request->file('image_mobile')->isValid()) {
                $newImageMobilePath = $request->file('image_mobile')->store('banners/mobile', 'public');
                Log::info('New mobile image: ' . $newImageMobilePath);
            }

            // 🔥 PERBAIKI: Update data
            $updateData = [
                'title' => $validated['title'] ?? null,
                'subtitle' => $validated['subtitle'] ?? null,
                'button_text' => $validated['button_text'] ?? null,
                'button_url' => $validated['button_url'] ?? null,
                'sort_order' => (int) ($validated['sort_order'] ?? 0),
                'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : false,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
            ];

            if ($newImagePath) {
                $updateData['image'] = $newImagePath;
            }

            if ($newImageMobilePath !== null) {
                $updateData['image_mobile'] = $newImageMobilePath;
            }

            $banner->update($updateData);

            Log::info('Banner updated successfully');

            // Hapus gambar lama
            if ($newImagePath && $oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
                Log::info('Deleted old desktop image: ' . $oldImage);
            }

            if ($newImageMobilePath && $oldImageMobile && Storage::disk('public')->exists($oldImageMobile)) {
                Storage::disk('public')->delete($oldImageMobile);
                Log::info('Deleted old mobile image: ' . $oldImageMobile);
            }

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal: ' . $e->getMessage());

        } catch (Throwable $e) {
            Log::error('Update banner error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Hapus gambar baru jika gagal
            if (isset($newImagePath) && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }
            if (isset($newImageMobilePath) && Storage::disk('public')->exists($newImageMobilePath)) {
                Storage::disk('public')->delete($newImageMobilePath);
            }

            return back()
                ->withInput()
                ->with('error', 'Banner gagal diperbarui: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(Banner $banner)
    {
        try {
            $imagePath = $banner->image;
            $imageMobilePath = $banner->image_mobile;

            $banner->delete();

            // Hapus file gambar
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            if ($imageMobilePath && Storage::disk('public')->exists($imageMobilePath)) {
                Storage::disk('public')->delete($imageMobilePath);
            }

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil dihapus.');

        } catch (Throwable $e) {
            Log::error('Delete banner error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Banner gagal dihapus: ' . $e->getMessage());
        }
    }
}