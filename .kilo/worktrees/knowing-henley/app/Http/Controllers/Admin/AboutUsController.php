<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUs::first();
        return view('admin.about.index', compact('about'));
    }

    public function update(Request $request)
    {
        Log::info('About Us Update Request', $request->all());

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'vision' => ['nullable', 'string'],
            'mission' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            DB::beginTransaction();

            $about = AboutUs::first();
            $isActive = $request->has('is_active') && $request->input('is_active') == '1';

            if ($about) {
                $about->update([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'vision' => $validated['vision'] ?? $about->vision,
                    'mission' => $validated['mission'] ?? $about->mission,
                    'version' => $validated['version'] ?? $about->version,
                    'effective_date' => $validated['effective_date'] ?? $about->effective_date,
                    'is_active' => $isActive,
                ]);
                Log::info('About Us updated', ['id' => $about->id]);
            } else {
                $about = AboutUs::create([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'vision' => $validated['vision'] ?? null,
                    'mission' => $validated['mission'] ?? null,
                    'version' => $validated['version'] ?? '1.0',
                    'effective_date' => $validated['effective_date'] ?? now(),
                    'is_active' => $isActive,
                ]);
                Log::info('About Us created', ['id' => $about->id]);
            }

            DB::commit();

            return redirect()
                ->route('admin.about.index')
                ->with('success', 'Tentang Kami berhasil disimpan.');

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('About Us save error', [
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
            $about = AboutUs::first();
            if ($about) {
                $about->update(['is_active' => !$about->is_active]);
                $status = $about->is_active ? 'diaktifkan' : 'dinonaktifkan';
                Log::info('About Us toggled', ['id' => $about->id, 'status' => $about->is_active]);
                return redirect()
                    ->route('admin.about.index')
                    ->with('success', "Tentang Kami berhasil {$status}.");
            }
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (Throwable $e) {
            Log::error('About Us toggle error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}