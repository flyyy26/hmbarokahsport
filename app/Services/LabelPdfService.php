<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class LabelPdfService
{
    private BarcodeGeneratorPNG $barcodeGenerator;

    public function __construct()
    {
        $this->barcodeGenerator = new BarcodeGeneratorPNG;
    }

    public function generateSingleLabel(Order $order): string
    {
        $order->loadMissing(['items', 'user']);
        $setting = Setting::first();
        $data = $this->prepareLabelData($order, $setting);

        // 🔥 PERBAIKAN: Set options dengan path yang benar
        return PdfFacade::loadView('admin.orders.label', $data)
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => base_path(),
            ])
            ->output();
    }

    public function generateBulkLabels(array $orders): string
    {
        $setting = Setting::first();
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">';
        $html .= '<style>@page { size: 100mm 150mm; margin: 0; }</style>';
        $html .= '<style>body { font-family: Arial, sans-serif; }</style>';
        $html .= '</head><body>';

        foreach ($orders as $index => $order) {
            $order->loadMissing(['items', 'user']);
            $data = $this->prepareLabelData($order, $setting);

            $labelHtml = view('admin.orders.label', $data)->render();

            if ($index > 0) {
                $html .= '<div style="page-break-before: always;">';
            } else {
                $html .= '<div>';
            }
            $html .= $labelHtml;
            $html .= '</div>';
        }

        $html .= '</body></html>';

        return PdfFacade::loadHTML($html)
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => base_path(),
            ])
            ->output();
    }

    private function prepareLabelData(Order $order, ?Setting $setting): array
    {
        $biteshipTrackingUrl = $this->resolveTrackingUrl($order);
        $qrCodeDataUri = $this->generateQrCodeDataUri($biteshipTrackingUrl);
        $barcodeDataUri = $this->generateBarcodeDataUri($order->tracking_number);
        $storeLogoUri = $this->resolveImageUri($setting->logo ?? 'logo.png');
        $courierLogoUri = $this->resolveCourierLogoUri($order->courier);

        return [
            'order' => $order,
            'setting' => $setting,
            'qrCodeUri' => $qrCodeDataUri,
            'barcodeUri' => $barcodeDataUri,
            'storeLogoUri' => $storeLogoUri,
            'courierLogoUri' => $courierLogoUri,
        ];
    }

    private function resolveTrackingUrl(Order $order): string
    {
        if ($order->biteship_tracking_url) {
            return $order->biteship_tracking_url;
        }

        if ($order->biteship_order_id) {
            return 'https://track.biteship.com/'.$order->biteship_order_id;
        }

        return 'https://track.biteship.com/'.($order->tracking_number ?? 'unknown');
    }

    private function generateQrCodeDataUri(string $url): string
    {
        try {
            $qrCode = Encoder::encode($url, ErrorCorrectionLevel::L());
            $matrix = $qrCode->getMatrix();
            $size = $matrix->getWidth();
            $moduleSize = 3;
            $margin = 2;
            $imgSize = ($size + $margin * 2) * $moduleSize;

            if (! function_exists('imagecreate')) {
                return '';
            }

            $img = imagecreate($imgSize, $imgSize);
            $black = imagecolorallocate($img, 0, 0, 0);
            $white = imagecolorallocate($img, 255, 255, 255);
            imagefill($img, 0, 0, $white);

            for ($x = 0; $x < $size; $x++) {
                for ($y = 0; $y < $size; $y++) {
                    if ($matrix->get($x, $y)) {
                        $px = ($x + $margin) * $moduleSize;
                        $py = ($y + $margin) * $moduleSize;
                        imagefilledrectangle($img, $px, $py, $px + $moduleSize - 1, $py + $moduleSize - 1, $black);
                    }
                }
            }

            ob_start();
            imagepng($img);
            $pngData = ob_get_clean();
            imagedestroy($img);

            return 'data:image/png;base64,'.base64_encode($pngData);
        } catch (\Exception $e) {
            return '';
        }
    }

    private function generateBarcodeDataUri(?string $trackingNumber): string
    {
        if (empty($trackingNumber)) {
            return '';
        }

        try {
            $barcode = $this->barcodeGenerator->getBarcode(
                $trackingNumber,
                BarcodeGenerator::TYPE_CODE_128
            );

            return 'data:image/png;base64,'.base64_encode($barcode);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * 🔥 PERBAIKAN UNTUK HOSTINGER
     */
    private function resolveImageUri(string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // Jika path sudah berupa URL atau data URI
        if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'data:')) {
            return $path;
        }

        // Bersihkan path
        $path = str_replace(['storage/', 'public/', 'public_html/'], '', $path);

        // 🔥 COBA DARI STORAGE
        try {
            if (Storage::disk('public')->exists($path)) {
                $data = Storage::disk('public')->get($path);
                $mime = Storage::disk('public')->mimeType($path);
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        } catch (\Exception $e) {
            // Skip
        }

        // 🔥 COBA DARI PUBLIC_HTML (Hostinger)
        $paths = [
            // Public HTML
            base_path('../public_html/' . $path),
            base_path('../public_html/images/' . $path),
            base_path('../public_html/uploads/' . $path),
            base_path('../public_html/storage/' . $path),
            base_path('../public_html/storage/images/' . $path),
            
            // Public path Laravel
            public_path($path),
            public_path('images/' . $path),
            public_path('uploads/' . $path),
            public_path('storage/' . $path),
            public_path('storage/images/' . $path),
            
            // Base path
            base_path('public/' . $path),
            base_path('public/images/' . $path),
            base_path('public/storage/' . $path),
            
            // Storage path
            storage_path('app/public/' . $path),
            storage_path('app/public/images/' . $path),
            storage_path('app/public/uploads/' . $path),
        ];

        foreach ($paths as $fullPath) {
            try {
                if (!empty($fullPath) && file_exists($fullPath) && is_readable($fullPath)) {
                    $mime = mime_content_type($fullPath);
                    if ($mime === false) {
                        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                        $mime = match(strtolower($ext)) {
                            'png' => 'image/png',
                            'jpg', 'jpeg' => 'image/jpeg',
                            'gif' => 'image/gif',
                            'webp' => 'image/webp',
                            'svg' => 'image/svg+xml',
                            default => 'image/png',
                        };
                    }
                    $data = file_get_contents($fullPath);
                    if ($data !== false) {
                        return 'data:' . $mime . ';base64,' . base64_encode($data);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return '';
    }

    private function resolveCourierLogoUri(?string $courier): string
    {
        if (! $courier) {
            return '';
        }

        $courierLower = strtolower($courier);

        // 🔥 COBA DARI STORAGE
        try {
            $extensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
            foreach ($extensions as $ext) {
                $storagePath = 'images/couriers/' . $courierLower . '.' . $ext;
                if (Storage::disk('public')->exists($storagePath)) {
                    $data = Storage::disk('public')->get($storagePath);
                    $mime = Storage::disk('public')->mimeType($storagePath);
                    return 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        } catch (\Exception $e) {
            // Skip
        }

        // 🔥 COBA DARI PUBLIC_HTML
        $paths = [];
        foreach (['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'] as $ext) {
            $paths[] = base_path('../public_html/images/couriers/' . $courierLower . '.' . $ext);
            $paths[] = base_path('../public_html/storage/images/couriers/' . $courierLower . '.' . $ext);
            $paths[] = public_path('images/couriers/' . $courierLower . '.' . $ext);
            $paths[] = public_path('storage/images/couriers/' . $courierLower . '.' . $ext);
            $paths[] = base_path('public/images/couriers/' . $courierLower . '.' . $ext);
            $paths[] = storage_path('app/public/images/couriers/' . $courierLower . '.' . $ext);
        }

        foreach ($paths as $fullPath) {
            try {
                if (!empty($fullPath) && file_exists($fullPath) && is_readable($fullPath)) {
                    $mime = mime_content_type($fullPath);
                    if ($mime === false) {
                        $mime = 'image/png';
                    }
                    $data = file_get_contents($fullPath);
                    if ($data !== false) {
                        return 'data:' . $mime . ';base64,' . base64_encode($data);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return '';
    }
}