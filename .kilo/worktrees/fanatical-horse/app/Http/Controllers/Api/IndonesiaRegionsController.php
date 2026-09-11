<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndonesiaRegionsController extends Controller
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('app.url') . '/api/indonesia-regions';
    }

    /**
     * GET /api/indonesia-regions/cascade
     * Ambil semua data wilayah berjenjang
     */
    public function cascade(Request $request)
    {
        try {
            $regionCode = $request->query('region_code');
            $countryName = $request->query('country_name', 'ID');

            $url = $this->baseUrl . '/cascade';
            if ($regionCode) {
                $url .= '?region_code=' . $regionCode . '&country_name=' . $countryName;
            } elseif ($countryName) {
                $url .= '?country_name=' . $countryName;
            }

            $response = Http::get($url);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            Log::error('IndonesiaRegions API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json(['error' => 'Gagal memuat data wilayah'], 500);

        } catch (\Exception $e) {
            Log::error('IndonesiaRegions Exception', [
                'message' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/indonesia-regions/provinces
     * Ambil semua provinsi
     */
    public function provinces()
    {
        try {
            $response = Http::get($this->baseUrl . '/cascade?country_name=ID');

            if ($response->successful()) {
                $data = $response->json();
                $provinces = $data['options']['provinces'] ?? [];
                return response()->json($provinces);
            }

            return response()->json(['error' => 'Gagal memuat provinsi'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/indonesia-regions/cities?province_code=11
     * Ambil kota berdasarkan provinsi
     */
    public function cities(Request $request)
    {
        try {
            $provinceCode = $request->query('province_code');

            if (!$provinceCode) {
                return response()->json(['error' => 'province_code required'], 400);
            }

            $response = Http::get($this->baseUrl . '/cascade', [
                'region_code' => $provinceCode,
                'country_name' => 'ID'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cities = $data['options']['cities'] ?? [];
                return response()->json($cities);
            }

            return response()->json(['error' => 'Gagal memuat kota'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/indonesia-regions/districts?city_code=11.01
     * Ambil kecamatan berdasarkan kota
     */
    public function districts(Request $request)
    {
        try {
            $cityCode = $request->query('city_code');

            if (!$cityCode) {
                return response()->json(['error' => 'city_code required'], 400);
            }

            $response = Http::get($this->baseUrl . '/cascade', [
                'region_code' => $cityCode,
                'country_name' => 'ID'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $districts = $data['options']['districts'] ?? [];
                return response()->json($districts);
            }

            return response()->json(['error' => 'Gagal memuat kecamatan'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/indonesia-regions/villages?district_code=11.01.01
     * Ambil kelurahan berdasarkan kecamatan
     */
    public function villages(Request $request)
    {
        try {
            $districtCode = $request->query('district_code');

            if (!$districtCode) {
                return response()->json(['error' => 'district_code required'], 400);
            }

            $response = Http::get($this->baseUrl . '/cascade', [
                'region_code' => $districtCode,
                'country_name' => 'ID'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $villages = $data['options']['villages'] ?? [];
                return response()->json($villages);
            }

            return response()->json(['error' => 'Gagal memuat kelurahan'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}