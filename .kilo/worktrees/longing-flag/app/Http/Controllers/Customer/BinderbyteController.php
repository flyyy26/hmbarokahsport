<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\BinderbyteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BinderbyteController extends Controller
{
    protected $binderbyte;

    public function __construct(BinderbyteService $binderbyte)
    {
        $this->binderbyte = $binderbyte;
    }

    public function provinces()
    {
        try {
            $provinces = $this->binderbyte->getProvinces();
            
            // Log untuk debugging
            Log::info('Provinces API called, count: ' . count($provinces));
            
            return response()->json($provinces);
        } catch (\Exception $e) {
            Log::error('Provinces API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cities(Request $request)
    {
        try {
            $request->validate([
                'province_id' => 'required|integer',
            ]);

            $cities = $this->binderbyte->getCities($request->province_id);
            
            Log::info('Cities API called for province: ' . $request->province_id . ', count: ' . count($cities));
            
            return response()->json($cities);
        } catch (\Exception $e) {
            Log::error('Cities API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cost(Request $request)
    {
        try {
            $request->validate([
                'origin' => 'required|integer',
                'destination' => 'required|integer',
                'weight' => 'required|integer|min:1',
                'courier' => 'required|string',
            ]);

            $results = $this->binderbyte->checkCost(
                $request->origin,
                $request->destination,
                $request->weight,
                $request->courier
            );

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Cost API Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}