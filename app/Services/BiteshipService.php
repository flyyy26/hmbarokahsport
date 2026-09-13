<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BiteshipService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.biteship.api_key');
        $this->baseUrl = 'https://api.biteship.com/v1';
    }

    /**
     * Get shipping rates from Biteship
     */
    public function getShippingRates(
        string $originPostalCode,
        string $destinationPostalCode,
        array $items,
        array $couriers = []
    ): array {
        if (empty($couriers)) {
            $couriers = [
                'jne',
                'jnt',
                'sicepat',
                'pos',
                'anteraja',
                'lion',
                'ninja',
                'rpx',
                'pahala',
                'wahana',
                'tiki',
                'ncs',
                'first',
                'idexpress',
                'star',
            ];
        }

        $formattedItems = collect($items)
            ->map(function ($item) {
                return [
                    'name' => $item['name'] ?? 'Product',
                    'value' => (int) ($item['value'] ?? $item['price'] ?? 0),
                    'weight' => max(1, (int) ($item['weight'] ?? 1000)),
                    'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                ];
            })
            ->values()
            ->all();

        $payload = [
            'origin_postal_code' => (int) $originPostalCode,
            'destination_postal_code' => (int) $destinationPostalCode,
            'couriers' => implode(',', array_unique($couriers)),
            'items' => $formattedItems,
        ];

        Log::info('BITESHIP RATES REQUEST', [
            'payload' => $payload,
        ]);

        $response = Http::timeout(30)
            ->acceptJson()
            ->withToken($this->apiKey)
            ->post($this->baseUrl . '/rates/couriers', $payload);

        $data = $response->json();

        Log::info('BITESHIP RATES RESPONSE', [
            'status' => $response->status(),
            'data' => $data,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                $data['message']
                    ?? $data['error']
                    ?? 'Gagal mendapatkan ongkir dari Biteship.'
            );
        }

        return $data;
    }

    /**
     * 🔥 GET TRACKING DETAILS
     */
    public function getTrackingDetails(string $biteshipOrderId, ?string $waybillId = null, ?string $trackingUrl = null): array
    {
        $cacheKey = 'biteship:tracking:' . md5($biteshipOrderId . '|' . ($waybillId ?? '') . '|' . ($trackingUrl ?? ''));
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $candidates = array_filter([
                $biteshipOrderId,
                $waybillId,
                $trackingUrl ? trim(parse_url($trackingUrl, PHP_URL_PATH), '/') : null,
            ]);

            foreach ($candidates as $candidate) {
                $response = Http::timeout(10)
                    ->acceptJson()
                    ->withToken($this->apiKey)
                    ->get($this->baseUrl . '/trackings/' . $candidate);

                if ($response->successful()) {
                    $data = $response->json();

                    // 🔥 Normalize: Biteship returns 'link' as the tracking URL.
                    // Map it to 'waybill_url' so all consumers use a consistent field name.
                    if (isset($data['link']) && !isset($data['waybill_url'])) {
                        $data['waybill_url'] = $data['link'];
                    }

                    // 🔥 Normalize message (Biteship API has a typo: "messsage")
                    $message = $data['message'] ?? $data['messsage'] ?? 'Tracking berhasil dimuat';

                    $result = [
                        'success' => true,
                        'message' => $message,
                        'data' => $data,
                    ];

                    Cache::put($cacheKey, $result, now()->addMinutes(5));

                    return $result;
                }
            }

            $result = [
                'success' => false,
                'message' => 'Gagal mendapatkan tracking',
                'data' => null,
            ];

            Cache::put($cacheKey, $result, now()->addMinutes(5));

            return $result;

        } catch (\Exception $e) {
            Log::error('BITESHIP TRACKING ERROR', [
                'order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * 🔥 Clear cached tracking data so the next call hits the Biteship API fresh.
     */
    public function clearTrackingCache(string $biteshipOrderId, ?string $waybillId = null, ?string $trackingUrl = null): void
    {
        $cacheKey = 'biteship:tracking:' . md5($biteshipOrderId . '|' . ($waybillId ?? '') . '|' . ($trackingUrl ?? ''));
        Cache::forget($cacheKey);
    }

    /**
     * 🔥 GET SHIPPING LABEL (PDF URL)
     */
    public function getWaybill(string $biteshipOrderId): array
    {
        $cacheKey = 'biteship:waybell:' . $biteshipOrderId;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->get($this->baseUrl . '/orders/' . $biteshipOrderId . '/label');

            $data = $response->json();

            if ($response->successful() && isset($data['shipping_label']['file_url'])) {
                $result = [
                    'success' => true,
                    'pdf_url' => $data['shipping_label']['file_url'],
                ];
                Cache::put($cacheKey, $result, now()->addMinutes(30));
                return $result;
            }

            $result = [
                'success' => false,
                'error' => $data['message'] ?? 'Gagal mengambil label',
            ];
            Cache::put($cacheKey, $result, now()->addMinutes(30));
            return $result;
        } catch (\Exception $e) {
            Log::error('BITESHIP WAYBILL ERROR', [
                'order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);
            $result = ['error' => $e->getMessage(), 'success' => false];
            Cache::put($cacheKey, $result, now()->addMinutes(30));
            return $result;
        }
    }

    /**
     * 🔥 GET SHIPPING LABEL PDF CONTENT (raw binary)
     */
    public function getWaybillContent(string $biteshipOrderId): array
    {
        $cacheKey = 'biteship:waybill_content:' . $biteshipOrderId;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $result = $this->getWaybill($biteshipOrderId);

        if (!$result['success']) {
            $errorResult = [
                'success' => false,
                'error' => $result['error'] ?? 'Gagal mengambil label',
            ];
            Cache::put($cacheKey, $errorResult, now()->addMinutes(10));
            return $errorResult;
        }

        try {
            $pdfResponse = Http::timeout(10)
                ->withHeaders([
                    'Accept' => 'application/pdf',
                    'Accept-Encoding' => 'gzip, deflate, br',
                ])
                ->get($result['pdf_url']);

            // Fallback: try without token if 401
            if (!$pdfResponse->successful() && $pdfResponse->status() === 401) {
                $pdfResponse = Http::timeout(10)
                    ->withHeaders(['Accept' => 'application/pdf'])
                    ->get($result['pdf_url']);
            }

            if ($pdfResponse->successful()) {
                $body = $pdfResponse->toPsrResponse()->getBody();
                $body->rewind();
                $pdfContent = $body->getContents();
                $result = [
                    'success' => true,
                    'pdf_base64' => base64_encode($pdfContent),
                    'content_length' => strlen($pdfContent),
                ];
                Cache::put($cacheKey, $result, now()->addMinutes(30));
                return $result;
            }

            // Fallback: return the URL itself so frontend can try
            $errorResult = [
                'success' => false,
                'error' => 'Gagal mengunduh file PDF (HTTP ' . $pdfResponse->status() . ').',
                'pdf_url' => $result['pdf_url'],
            ];
            Cache::put($cacheKey, $errorResult, now()->addMinutes(5));
            return $errorResult;
        } catch (\Exception $e) {
            Log::error('BITESHIP WAYBILL CONTENT ERROR', [
                'order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);
            $errorResult = ['error' => $e->getMessage(), 'success' => false, 'pdf_url' => $result['pdf_url']];
            Cache::put($cacheKey, $errorResult, now()->addMinutes(5));
            return $errorResult;
        }
    }

    /**
     * 🔥 GET SHIPPING LABEL AS RESIZED PDF (100mm x 150mm)
     */
    public function getWaybillResized(string $biteshipOrderId, float $widthMm = 100, float $heightMm = 150): array
    {
        $cacheKey = 'biteship:waybill_resized:' . $biteshipOrderId . ':' . $widthMm . 'x' . $heightMm;
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        $result = $this->getWaybillContent($biteshipOrderId);

        if (!$result['success']) {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Gagal mengambil label',
            ];
        }

        try {
            $pdfContent = base64_decode($result['pdf_base64']);

            $tempInput = tempnam(sys_get_temp_dir(), 'biteship_pdf_');
            file_put_contents($tempInput, $pdfContent);

            $pdf = new \setasign\Fpdi\Fpdi();

            $pageCount = $pdf->setSourceFile($tempInput);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $originalSize = $pdf->getTemplateSize($templateId);
                $orientation = ($widthMm > $heightMm) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$widthMm, $heightMm]);
                $pdf->useTemplate($templateId, 0, 0, $widthMm, $heightMm, false);
            }

            $resizedPdf = $pdf->Output('S', 'label.pdf');

            unlink($tempInput);

            $returnResult = [
                'success' => true,
                'pdf_base64' => base64_encode($resizedPdf),
                'content_length' => strlen($resizedPdf),
            ];

            Cache::put($cacheKey, $returnResult, now()->addMinutes(10));

            return $returnResult;
        } catch (\Exception $e) {
            Log::error('BITESHIP WAYBILL RESIZED ERROR', [
                'order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage(), 'success' => false];
        }
    }
    public function trackOrder(string $biteshipOrderId, ?string $waybillId = null, ?string $trackingUrl = null)
    {
        $candidates = array_filter([
            $biteshipOrderId,
            $waybillId,
            $trackingUrl ? trim(parse_url($trackingUrl, PHP_URL_PATH), '/') : null,
        ]);

        foreach ($candidates as $candidate) {
            if (!$candidate) {
                continue;
            }

            $response = Http::timeout(10)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->get($this->baseUrl . '/trackings/' . $candidate);

            if ($response->successful()) {
                return $response->json();
            }
        }

        return [
            'success' => false,
            'message' => 'Failed to retrieve tracking number.',
            'data' => null,
        ];
    }

    /**
     * Search location/area
     */
    public function searchArea(string $query, int $limit = 10)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/maps/areas', [
            'q' => $query,
            'limit' => $limit,
        ]);

        return $response->json();
    }

    /**
     * 🔥 GET COURIER RATES BY POSTAL CODE
     */
    public function getRatesByPostalCode(array $data): array
    {
         try {
            $setting = \App\Models\Setting::first();

            $originPostalCode = (int) ($setting->postal_code ?? config('services.biteship.origin_postal_code', 46191));

            $payload = [
                'origin_postal_code' => $originPostalCode,
                'destination_postal_code' => (int) $data['destination_postal_code'],
                'items' => collect($data['items'])->map(function ($item) {
                    return [
                        'name' => $item['name'],
                        'value' => (int) $item['price'],
                        'weight' => (int) $item['weight'],
                        'quantity' => (int) $item['quantity'],
                    ];
                })->values()->all(),
            ];

            $response = Http::timeout(30)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->post($this->baseUrl . '/rates/couriers', $payload);

            $result = $response->json();

            if ($response->failed()) {
                return [
                    'error' => $result['message'] ?? $result['error'] ?? 'Gagal mendapatkan ongkir',
                    'detail' => $result['detail'] ?? null,
                ];
            }

            // Format response untuk frontend
            $formatted = [];
            foreach ($result['pricing'] ?? [] as $rate) {
                $courierCode = $rate['courier_code'] ?? $rate['company'] ?? 'unknown';
                if (!isset($formatted[$courierCode])) {
                    $formatted[$courierCode] = [
                        'code' => $courierCode,
                        'name' => $rate['courier_name'] ?? $rate['company'] ?? strtoupper($courierCode),
                        'services' => [],
                    ];
                }
                $formatted[$courierCode]['services'][] = [
                    'service' => $rate['courier_service_code'] ?? $rate['service_code'] ?? 'regular',
                    'name' => $rate['courier_service_name'] ?? $rate['service_name'] ?? 'Reguler',
                    'description' => $rate['description'] ?? '',
                    'cost' => (int) ($rate['price'] ?? 0),
                    'etd' => $rate['duration'] ?? $rate['shipment_duration_range'] ?? '-',
                ];
            }

            return array_values($formatted);

        } catch (\Exception $e) {
            Log::error('Biteship getRatesByPostalCode error: ' . $e->getMessage());
            return [
                'error' => 'Gagal mendapatkan ongkir: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * 🔥 CREATE ORDER
     */
    public function createOrder(array $data): array
    {
        try {
            $setting = \App\Models\Setting::first();

             $originPostalCode = (int) ($setting->postal_code ?? config('services.biteship.origin_postal_code', 46191));

            $originAddress = $setting->address ?? 'Jl. Contoh No. 123';
            if ($setting->district) {
                $originAddress = $setting->address . ', ' . $setting->district;
            }

            $items = collect($data['items'])->map(function ($item) {
                return [
                    'name' => $item['name'] ?? 'Product',
                    'value' => (int) ($item['price'] ?? $item['value'] ?? 0),
                    'weight' => (int) ($item['weight'] ?? 1000),
                    'quantity' => (int) ($item['quantity'] ?? 1),
                ];
            })->values()->all();

            $payload = [
                'origin_contact_name' => $setting->store_name ?? config('app.name', 'Barokah Sport'),
                'origin_contact_phone' => $setting->phone ?? '08123456789',
                'origin_address' => $originAddress,
                'origin_postal_code' => $originPostalCode,
                'destination_contact_name' => $data['shipping_name'] ?? $data['recipient_name'] ?? $data['destination_contact_name'],
                'destination_contact_phone' => $data['shipping_phone'] ?? $data['recipient_phone'] ?? $data['destination_contact_phone'],
                'destination_address' => $data['shipping_address'] ?? $data['recipient_address'] ?? $data['destination_address'],
                'destination_postal_code' => (int) ($data['shipping_postal_code'] ?? $data['recipient_postal_code'] ?? $data['destination_postal_code'] ?? 0),
                'courier_company' => strtolower($data['courier'] ?? $data['courier_company'] ?? 'jne'),
                'courier_type' => strtolower($data['courier_type'] ?? $data['service'] ?? $data['service_code'] ?? 'reg'),
                'delivery_type' => $data['delivery_type'] ?? 'now',
                'items' => $items,
                'order_number' => $data['order_number'] ?? $data['order_id'] ?? 'ORD-' . time(),
            ];

        Log::info('BITESHIP CREATE ORDER REQUEST', [
            'payload' => $payload,
        ]);

        $response = Http::timeout(30)
            ->acceptJson()
            ->withToken($this->apiKey)
            ->post($this->baseUrl . '/orders', $payload);

        $result = $response->json();

        Log::info('BITESHIP CREATE ORDER RESPONSE', [
            'status' => $response->status(),
            'response' => $result,
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'message' => $result['error'] ?? $result['message'] ?? 'Gagal membuat order di Biteship',
                'detail' => $result['detail'] ?? null,
                'data' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'Order berhasil dibuat di Biteship',
            'data' => $result,
        ];

    } catch (\Exception $e) {
        Log::error('BITESHIP CREATE ORDER ERROR', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return [
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'data' => null,
        ];
    }
}

    public function testApiKey(): array
    {
        try {
            // 🔥 GUNAKAN ENDPOINT YANG VALID UNTUK TEST
            // Endpoint /rates/couriers adalah endpoint yang valid untuk test
            $response = Http::timeout(10)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->get($this->baseUrl . '/rates/couriers', [
                    'origin_postal_code' => 46191,
                    'destination_postal_code' => 46191,
                    'couriers' => 'jne',
                    'items' => [
                        [
                            'name' => 'Test Product',
                            'value' => 10000,
                            'weight' => 1000,
                            'quantity' => 1,
                        ]
                    ]
                ]);

            $data = $response->json();

            Log::info('BITESHIP TEST API KEY RESPONSE', [
                'status' => $response->status(),
                'response' => $data,
            ]);

            // 🔥 CEK RESPONSE
            if ($response->successful()) {
                return ['success' => true, 'message' => 'API Key valid dan aktif'];
            }

            // 🔥 HANDLE ERROR
            $error = $data['error'] ?? $data['message'] ?? 'Unknown error';
            
            if ($response->status() === 404) {
                return [
                    'success' => false,
                    'message' => '⚠️ Endpoint tidak ditemukan. Coba gunakan endpoint yang valid.',
                    'status' => 'error',
                    'detail' => $data
                ];
            }

            if (str_contains($error, 'Key has not been activated')) {
                return [
                    'success' => false,
                    'message' => '❌ API Key belum diaktifkan. Silakan aktifkan di dashboard Biteship.',
                    'status' => 'inactive'
                ];
            }

            return [
                'success' => false,
                'message' => '❌ ' . $error,
                'status' => 'error',
                'detail' => $data
            ];

        } catch (\Exception $e) {
            Log::error('BITESHIP TEST API KEY ERROR', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => '❌ Terjadi kesalahan: ' . $e->getMessage(),
                'status' => 'exception'
            ];
        }
    }

    /**
     * 🔥 Clear cached label data for an order (forces fresh API call on next fetch)
     */
    public function clearLabelCache(string $biteshipOrderId): void
    {
        Cache::forget('biteship:waybell:' . $biteshipOrderId);
        Cache::forget('biteship:waybell_content:' . $biteshipOrderId);
        Cache::forget('biteship:waybill_resized:' . $biteshipOrderId . ':100x150');
    }
}