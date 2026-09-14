<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\PromoBar;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Http\Controllers\Customer\CustomerHomeController;

class BannerController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
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
        Log::info('=== STORE BANNER ===');

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

            $uploadedFiles = [];

            // 🔥 CONVERT DESKTOP IMAGE KE WEBP
            $imagePath = $this->imageOptimizer->convertToWebp(
                file: $request->file('image'),
                folder: 'banners',
                maxWidth: 1920,  // cukup untuk banner desktop
                quality: 82
            );
            $uploadedFiles[] = $imagePath;
            Log::info('Desktop WebP uploaded: ' . $imagePath);

            // 🔥 CONVERT MOBILE IMAGE KE WEBP (kalau ada)
            $imageMobilePath = null;
            if ($request->hasFile('image_mobile') && $request->file('image_mobile')->isValid()) {
                $imageMobilePath = $this->imageOptimizer->convertToWebp(
                    file: $request->file('image_mobile'),
                    folder: 'banners/mobile',
                    maxWidth: 800,  // cukup untuk mobile
                    quality: 82
                );
                $uploadedFiles[] = $imageMobilePath;
                Log::info('Mobile WebP uploaded: ' . $imageMobilePath);
            }

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

            Log::info('Banner created, ID: ' . $banner->id);

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil ditambahkan (otomatis dikonversi ke WebP).');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (Throwable $e) {
            Log::error('Store banner error: ' . $e->getMessage());

            // Cleanup file yang sudah terupload
            foreach ($uploadedFiles ?? [] as $path) {
                $this->imageOptimizer->delete($path);
            }

            return back()->withInput()->with('error', 'Banner gagal ditambahkan: ' . $e->getMessage());
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
        Log::info('=== UPDATE BANNER === ID: ' . $banner->id);

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

            $oldImage = $banner->image;
            $oldImageMobile = $banner->image_mobile;
            $newImagePath = null;
            $newImageMobilePath = null;

            // 🔥 CONVERT DESKTOP IMAGE KE WEBP
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $newImagePath = $this->imageOptimizer->convertToWebp(
                    file: $request->file('image'),
                    folder: 'banners',
                    maxWidth: 1920,
                    quality: 82
                );
                Log::info('New desktop WebP: ' . $newImagePath);
            }

            // 🔥 CONVERT MOBILE IMAGE KE WEBP
            if ($request->hasFile('image_mobile') && $request->file('image_mobile')->isValid()) {
                $newImageMobilePath = $this->imageOptimizer->convertToWebp(
                    file: $request->file('image_mobile'),
                    folder: 'banners/mobile',
                    maxWidth: 800,
                    quality: 82
                );
                Log::info('New mobile WebP: ' . $newImageMobilePath);
            }

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

            // Hapus gambar lama setelah update sukses
            if ($newImagePath && $oldImage) {
                $this->imageOptimizer->delete($oldImage);
            }
            if ($newImageMobilePath && $oldImageMobile) {
                $this->imageOptimizer->delete($oldImageMobile);
            }

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil diperbarui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (Throwable $e) {
            Log::error('Update banner error: ' . $e->getMessage());

            // Hapus gambar baru kalau update gagal
            if (isset($newImagePath)) $this->imageOptimizer->delete($newImagePath);
            if (isset($newImageMobilePath)) $this->imageOptimizer->delete($newImageMobilePath);

            return back()->withInput()->with('error', 'Banner gagal diperbarui: ' . $e->getMessage());
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

            // Hapus via helper
            $this->imageOptimizer->delete($imagePath);
            $this->imageOptimizer->delete($imageMobilePath);

            CustomerHomeController::clearCache();

            return redirect()
                ->route('admin.banners.index')
                ->with('success', 'Banner berhasil dihapus.');

        } catch (Throwable $e) {
            Log::error('Delete banner error: ' . $e->getMessage());
            return back()->with('error', 'Banner gagal dihapus: ' . $e->getMessage());
        }
    }
}