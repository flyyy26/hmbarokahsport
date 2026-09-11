<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    protected RajaOngkirService $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Get provinces - dari database
     */
    public function provinces()
    {
        try {
            return response()->json(
                $this->rajaOngkir->getProvinces()
            );

        } catch (\Throwable $e) {
            Log::error('Provinces API Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memuat data provinsi.'
            ], 500);
        }
    }

    /**
     * Get cities - dari database
     */
    public function cities(Request $request)
    {
        $request->validate([
            'province_id' => 'required|integer',
        ]);

        try {
            return response()->json(
                $this->rajaOngkir->getCities(
                    $request->integer('province_id')
                )
            );

        } catch (\Throwable $e) {
            Log::error('Cities API Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memuat data kota.'
            ], 500);
        }
    }

    /**
     * Get districts - dari database
     */
    public function districts(Request $request)
    {
        $request->validate([
            'city_code' => 'required|string',
        ]);

        try {
            $districts = \DB::table('indonesia_regions')
                ->where('status', 'active')
                ->where('code', 'LIKE', $request->city_code . '.%')
                ->whereRaw("LENGTH(code) = 6")
                ->orderBy('name')
                ->get(['code', 'name']);

            return response()->json($districts);

        } catch (\Throwable $e) {
            Log::error('Districts API Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memuat data kecamatan.'
            ], 500);
        }
    }

    /**
     * Get subdistricts - dari database
     */
    public function subdistricts(Request $request)
    {
        $request->validate([
            'district_code' => 'required|string',
        ]);

        try {
            $subdistricts = \DB::table('indonesia_regions')
                ->where('status', 'active')
                ->where('code', 'LIKE', $request->district_code . '.%')
                ->whereRaw("LENGTH(code) = 8")
                ->orderBy('name')
                ->get(['code', 'name', 'postal_code']);

            Log::info('Subdistricts API Response', [
                'count' => count($subdistricts),
            ]);

            return response()->json($subdistricts);

        } catch (\Throwable $e) {
            Log::error('Subdistricts API Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memuat data kelurahan.'
            ], 500);
        }
    }

    /**
     * Calculate shipping cost
     */
    public function cost(Request $request)
    {
        try {
            $request->validate([
                'origin' => 'required|string',
                'destination' => 'required|string',
                'weight' => 'required|integer|min:1',
                'courier' => 'nullable|string', // 🔥 Bisa null, 'all', atau spesifik
            ]);

            $courier = $request->input('courier', 'all');
            
            // 🔥 Jika 'all', gunakan 'jne' sebagai default (API Komerce hanya support 1 kurir per request)
            // Atau kita bisa loop beberapa kurir
            if ($courier === 'all') {
                $courier = 'jne'; // Default
            }

            Log::info('RajaOngkir Controller Cost Request', [
                'origin' => $request->input('origin'),
                'destination' => $request->input('destination'),
                'weight' => $request->input('weight'),
                'courier' => $courier,
            ]);

            $results = $this->rajaOngkir->checkCost(
                $request->string('origin')->toString(),
                $request->string('destination')->toString(),
                $request->integer('weight'),
                $courier
            );

            return response()->json([
                'success' => true,
                'data' => $results,
            ]);

        } catch (\Throwable $e) {
            Log::error('RajaOngkir Cost Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ongkir: ' . $e->getMessage(),
            ], 500);
        }
    }
}