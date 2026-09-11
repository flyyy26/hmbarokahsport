<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $privacy = PrivacyPolicy::first();
        return view('admin.privacy.index', compact('privacy'));
    }

    public function update(Request $request)
    {
        // Debug: Log data masuk
        Log::info('Privacy Policy Update Request', $request->all());
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            DB::beginTransaction();
            
            $privacy = PrivacyPolicy::first();
            $isActive = $request->has('is_active') && $request->input('is_active') == '1';

            if ($privacy) {
                $privacy->update([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? $privacy->version,
                    'effective_date' => $validated['effective_date'] ?? $privacy->effective_date,
                    'is_active' => $isActive,
                ]);
                Log::info('Privacy Policy updated', ['id' => $privacy->id, 'is_active' => $isActive]);
            } else {
                $privacy = PrivacyPolicy::create([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? '1.0',
                    'effective_date' => $validated['effective_date'] ?? now(),
                    'is_active' => $isActive,
                ]);
                Log::info('Privacy Policy created', ['id' => $privacy->id, 'is_active' => $isActive]);
            }

            DB::commit();

            return redirect()
                ->route('admin.privacy.index')
                ->with('success', 'Kebijakan Privasi berhasil disimpan.');

        } catch (Throwable $e) {
            DB::rollBack();
            
            Log::error('Privacy Policy save error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function toggle(Request $request)
    {
        try {
            $privacy = PrivacyPolicy::first();
            if ($privacy) {
                $privacy->update(['is_active' => !$privacy->is_active]);
                $status = $privacy->is_active ? 'diaktifkan' : 'dinonaktifkan';
                Log::info('Privacy Policy toggled', ['id' => $privacy->id, 'status' => $privacy->is_active]);
                return redirect()
                    ->route('admin.privacy.index')
                    ->with('success', "Kebijakan Privasi berhasil {$status}.");
            }
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (Throwable $e) {
            Log::error('Privacy Policy toggle error', [
                'message' => $e->getMessage()
            ]);
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}