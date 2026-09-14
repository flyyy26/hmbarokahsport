<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class ImageOptimizer
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Convert & simpan gambar ke WebP.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder      Folder di disk 'public' (mis. 'banners')
     * @param  int           $maxWidth    Max width (px). Height auto-scale.
     * @param  int           $quality     Kualitas WebP (0-100)
     * @return string        Path relatif di disk 'public'
     */
    public function convertToWebp(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 1600,
        int $quality = 82
    ): string {
        // 1. Baca file
        $image = $this->manager->read($file->getRealPath());

        // 2. Resize kalau lebih besar dari maxWidth (height auto)
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // 3. Encode ke WebP
        $encoded = $image->toWebp($quality);

        // 4. Generate nama file unik
        $filename = Str::random(40) . '.webp';
        $path = trim($folder, '/') . '/' . $filename;

        // 5. Simpan ke disk 'public'
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Hapus file dari disk 'public'.
     */
    public function delete(?string $path): bool
    {
        if (!$path) return false;
        if (!Storage::disk('public')->exists($path)) return false;
        return Storage::disk('public')->delete($path);
    }
}