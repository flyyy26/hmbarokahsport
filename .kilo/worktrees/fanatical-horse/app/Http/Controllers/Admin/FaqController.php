<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::with('category')->ordered()->get();
        $categories = FaqCategory::ordered()->get();
        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        $categories = FaqCategory::ordered()->get();
        $faqs = Faq::all();
        return view('admin.faqs.create', compact('categories', 'faqs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['required', 'exists:faq_categories,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            Faq::create([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'category_id' => $validated['category_id'],
                'order' => $validated['order'] ?? Faq::count() + 1,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'FAQ berhasil ditambahkan.');

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'FAQ gagal ditambahkan: ' . $e->getMessage());
        }
    }

    public function edit(Faq $faq)
    {
        $categories = FaqCategory::ordered()->get();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['required', 'exists:faq_categories,id'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $faq->update([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'category_id' => $validated['category_id'],
                'order' => $validated['order'] ?? $faq->order,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'FAQ berhasil diperbarui.');

        } catch (Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'FAQ gagal diperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();
            return redirect()
                ->route('admin.faqs.index')
                ->with('success', 'FAQ berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', 'FAQ gagal dihapus: ' . $e->getMessage());
        }
    }

    public function toggle(Faq $faq)
    {
        try {
            $faq->update(['is_active' => !$faq->is_active]);
            $status = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()
                ->route('admin.faqs.index')
                ->with('success', "FAQ berhasil {$status}.");
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal mengubah status FAQ.');
        }
    }
}