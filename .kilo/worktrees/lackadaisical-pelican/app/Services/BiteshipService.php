<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            $candidates = array_filter([
                $biteshipOrderId,
                $waybillId,
                $trackingUrl ? trim(parse_url($trackingUrl, PHP_URL_PATH), '/') : null,
            ]);

            foreach ($candidates as $candidate) {
                $response = Http::timeout(30)
                    ->acceptJson()
                    ->withToken($this->apiKey)
                    ->get($this->baseUrl . '/trackings/' . $candidate);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'success' => true,
                        'message' => 'Tracking berhasil dimuat',
                        'data' => $data,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan tracking',
                'data' => null,
            ];

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
     * 🔥 GET SHIPPING LABEL (PDF URL)
     */
    public function getWaybill(string $biteshipOrderId): array
    {
        try {
            $response = Http::timeout(30)
                ->acceptJson()
                ->withToken($this->apiKey)
                ->get($this->baseUrl . '/orders/' . $biteshipOrderId . '/label');

            $data = $response->json();

            if ($response->successful() && isset($data['shipping_label']['file_url'])) {
                return [
                    'success' => true,
                    'pdf_url' => $data['shipping_label']['file_url'],
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Gagal mengambil label',
            ];
        } catch (\Exception $e) {
            Log::error('BITESHIP WAYBILL ERROR', [
                'order_id' => $biteshipOrderId,
                'error' => $e->getMessage(),
            ]);
            return ['error' => $e->getMessage(), 'success' => false];
        }
    }

    /**
     * Track an order
     */
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

            $response = Http::timeout(30)
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
            $payload = [
                'origin_postal_code' => (int) config('services.biteship.origin_postal_code', 46191),
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
                'origin_address' => $setting->address ?? 'Jl. Contoh No. 123',
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
}