<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBar;
use Illuminate\Http\Request;

class PromoBarController extends Controller
{
    public function index()
    {
        $promoBar = PromoBar::first();
        return view('admin.promo-bar.index', compact('promoBar'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'text_left' => ['nullable', 'string', 'max:500'],
            'text_right' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $promoBar = PromoBar::first();

        if ($promoBar) {
            $promoBar->update([
                'text_left' => $validated['text_left'] ?? null,
                'text_right' => $validated['text_right'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        } else {
            PromoBar::create([
                'text_left' => $validated['text_left'] ?? null,
                'text_right' => $validated['text_right'] ?? null,
                'is_active' => $validated['is_active'] ?? false,
            ]);
        }

        return redirect()
            ->route('admin.promo-bar.index')
            ->with('success', 'Promo Bar berhasil diperbarui.');
    }
}