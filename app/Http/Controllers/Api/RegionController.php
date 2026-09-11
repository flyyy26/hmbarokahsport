<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IndonesiaRegion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    /**
     * GET /api/regions/provinces
     * 
     * Mengambil semua provinsi (level 1)
     */
    public function provinces(): JsonResponse
    {
        try {
            // 🔥 PERBAIKAN: Ambil code dan name dari database
            $provinces = IndonesiaRegion::query()
                ->where('status', 'active')
                ->whereRaw("LENGTH(code) = 2 AND code NOT LIKE '%.%'")
                ->orderBy('name')
                ->get(['code', 'name']);

            Log::info('Provinces loaded:', ['count' => $provinces->count()]);

            return response()->json($provinces);

        } catch (\Exception $e) {
            Log::error('Error loading provinces:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat provinsi'], 500);
        }
    }

    /**
     * GET /api/regions/cities?province_code=11
     * 
     * Mengambil Kabupaten/Kota berdasarkan Provinsi (level 2)
     */
    public function cities(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'province_code' => ['required', 'string'],
            ]);

            $provinceCode = $request->province_code;

            // Kode kota memakai format provinsi.nomor kota, misalnya 11.01.
            $cities = IndonesiaRegion::query()
                ->where('status', 'active')
                ->where('code', 'LIKE', $provinceCode . '.%')
                ->whereRaw("LENGTH(code) = 5")
                ->orderBy('name')
                ->get(['code', 'name']);

            Log::info('Cities loaded:', [
                'province_code' => $provinceCode,
                'count' => $cities->count()
            ]);

            return response()->json($cities);

        } catch (\Exception $e) {
            Log::error('Error loading cities:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat kota'], 500);
        }
    }

    /**
     * GET /api/regions/districts?city_code=11.01
     * 
     * Mengambil Kecamatan berdasarkan Kabupaten/Kota (level 3)
     */
    public function districts(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'city_code' => ['required', 'string'],
            ]);

            $cityCode = $request->city_code;

            // Kode kecamatan memakai format provinsi.kota.nomor kecamatan.
            $districts = IndonesiaRegion::query()
                ->where('status', 'active')
                ->where('code', 'LIKE', $cityCode . '.%')
                ->whereRaw("LENGTH(code) = 8")
                ->orderBy('name')
                ->get(['code', 'name']);

            Log::info('Districts loaded:', [
                'city_code' => $cityCode,
                'count' => $districts->count()
            ]);

            return response()->json($districts);

        } catch (\Exception $e) {
            Log::error('Error loading districts:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat kecamatan'], 500);
        }
    }

    /**
     * GET /api/regions/subdistricts?district_code=11.01.01
     * 
     * Mengambil Kelurahan/Desa berdasarkan Kecamatan (level 4)
     * Sekaligus mengembalikan kode pos
     */
    public function subdistricts(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'district_code' => ['required', 'string'],
            ]);

            $districtCode = $request->district_code;

            // Kode kelurahan memakai format provinsi.kota.kecamatan.nomor kelurahan.
            $subdistricts = IndonesiaRegion::query()
                ->where('status', 'active')
                ->where('code', 'LIKE', $districtCode . '.%')
                ->whereRaw("LENGTH(code) = 13")
                ->orderBy('name')
                ->get(['code', 'name', 'postal_code']);

            Log::info('Subdistricts loaded:', [
                'district_code' => $districtCode,
                'count' => $subdistricts->count()
            ]);

            return response()->json($subdistricts);

        } catch (\Exception $e) {
            Log::error('Error loading subdistricts:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memuat kelurahan'], 500);
        }
    }

    /**
     * GET /api/regions/search?q=bandung
     * 
     * Pencarian wilayah
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => ['required', 'string', 'min:2'],
            ]);

            $keyword = trim($request->q);

            $regions = IndonesiaRegion::query()
                ->where('status', 'active')
                ->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('search_text', 'LIKE', '%' . $keyword . '%');
                })
                ->orderBy('name')
                ->limit(50)
                ->get(['code', 'name', 'postal_code']);

            return response()->json($regions);

        } catch (\Exception $e) {
            Log::error('Error searching regions:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal mencari wilayah'], 500);
        }
    }
}