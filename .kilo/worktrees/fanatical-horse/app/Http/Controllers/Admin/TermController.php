<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TermController extends Controller
{
    public function index()
    {
        $term = Term::first();
        return view('admin.terms.index', compact('term'));
    }

    public function update(Request $request)
    {
        // Debug: cek data yang masuk
        // dd($request->all());
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $term = Term::first();

            if ($term) {
                $term->update([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? $term->version,
                    'effective_date' => $validated['effective_date'] ?? $term->effective_date,
                    'is_active' => $request->has('is_active') ? true : false,
                ]);
            } else {
                Term::create([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'version' => $validated['version'] ?? '1.0',
                    'effective_date' => $validated['effective_date'] ?? now(),
                    'is_active' => $request->has('is_active') ? true : false,
                ]);
            }

            return redirect()
                ->route('admin.terms.index')
                ->with('success', 'Syarat & Ketentuan berhasil disimpan.');

        } catch (Throwable $e) {
            // Debug: lihat error
            // dd($e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function toggle(Request $request)
    {
        try {
            $term = Term::first();
            if ($term) {
                $term->update(['is_active' => !$term->is_active]);
                $status = $term->is_active ? 'diaktifkan' : 'dinonaktifkan';
                return redirect()
                    ->route('admin.terms.index')
                    ->with('success', "Syarat & Ketentuan berhasil {$status}.");
            }
            return back()->with('error', 'Data tidak ditemukan.');
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}