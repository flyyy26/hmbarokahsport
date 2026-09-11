<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VillageController extends Controller
{
    public function getVillages(Request $request)
    {
        try {
            $districtCode = $request->query('district_code');
            
            if (!$districtCode) {
                return response()->json([
                    'error' => 'district_code required'
                ], 400);
            }

            Log::info('Getting villages for district:', ['district_code' => $districtCode]);

            // 🔥 PERBAIKAN: Hapus LENGTH(code) = 8 karena panjang code berbeda
            $villages = DB::table('indonesia_regions')
                ->where('status', 'active')
                ->where('code', 'LIKE', $districtCode . '.%')
                ->orderBy('name')
                ->get(['code', 'name', 'postal_code']);

            Log::info('Villages found:', ['count' => $villages->count()]);

            // Ubah format agar sesuai dengan format package
            $result = $villages->map(function($item) {
                return [
                    'value' => $item->code,
                    'label' => $item->name,
                    'postal_code' => $item->postal_code ?? ''
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error loading villages:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Gagal memuat kelurahan',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}