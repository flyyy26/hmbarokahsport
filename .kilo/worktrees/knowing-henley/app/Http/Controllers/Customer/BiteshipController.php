<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BiteshipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiteshipController extends Controller
{
    protected $biteship;

    public function __construct(BiteshipService $biteship)
    {
        $this->biteship = $biteship;
    }

    /**
     * Search location (autocomplete)
     */
    public function searchLocation(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $result = $this->biteship->searchLocation($request->q);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
                'data' => []
            ], 400);
        }

        // Format response untuk frontend
        $formatted = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $item) {
                $formatted[] = [
                    'label' => $item['formatted_address'] ?? $item['address'] ?? '',
                    'value' => $item['formatted_address'] ?? $item['address'] ?? '',
                    'lat' => $item['latitude'] ?? null,
                    'lng' => $item['longitude'] ?? null,
                    'postal_code' => $item['postal_code'] ?? null,
                    'province' => $item['province'] ?? null,
                    'city' => $item['city'] ?? null,
                    'district' => $item['district'] ?? null,
                    'subdistrict' => $item['subdistrict'] ?? null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }

    /**
     * Get shipping rates (cek ongkir)
     */
    public function getRates(Request $request)
    {
        try {
            $request->validate([
                'destination_postal_code' => 'required|string|min:4|max:10',
                'items' => 'required|array',
                'items.*.name' => 'required|string',
                'items.*.weight' => 'required|numeric|min:1',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
            ]);

            Log::info('Biteship Rates Request:', $request->all());

            $rates = $this->biteship->getRatesByPostalCode($request->all());

            // Jika ada error dengan detail
            if (isset($rates['error'])) {
                $message = $rates['error'];
                $detail = $rates['detail'] ?? null;
                
                Log::error('Biteship Rates Error:', [
                    'message' => $message,
                    'detail' => $detail
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'detail' => $detail,
                    'data' => []
                ], 400);
            }

            // Jika tidak ada error tapi data kosong
            if (empty($rates)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada layanan pengiriman tersedia untuk rute ini',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $rates
            ]);

        } catch (\Exception $e) {
            Log::error('Biteship Controller Exception: ' . $e->getMessage());
            Log::error('Biteship Controller Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Create shipping order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string',
            'shipping_phone' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_postal_code' => 'required|string',
            'courier_code' => 'required|string',
            'service_code' => 'required|string',
            'items' => 'required|array',
            'order_number' => 'required|string',
        ]);

        $result = $this->biteship->createOrder($request->all());

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Track shipment
     */
    public function trackOrder($orderId)
    {
        $result = $this->biteship->trackOrder($orderId);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Get waybill (cetak resi)
     */
    public function getWaybill($orderId)
    {
        $result = $this->biteship->getWaybill($orderId);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}