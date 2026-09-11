<?php

namespace App\Services; // or App\Http\Services

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
     * Track an order
     */
    public function trackOrder(string $biteshipOrderId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/orders/' . $biteshipOrderId . '/tracking');

        return $response->json();
    }

    /**
     * Get waybill
     */
    public function getWaybill(string $biteshipOrderId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/orders/' . $biteshipOrderId . '/waybill');

        return $response->json();
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
}
