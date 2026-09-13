<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerCareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::active()->published();

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%');
            });
        }

        $careers = $query->latest()->paginate(9);

        $departments = Career::active()->published()
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('customer.careers.index', compact('careers', 'departments'));
    }

    public function show(string $slug)
    {
        $career = Career::active()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Career::active()
            ->published()
            ->where('id', '!=', $career->id)
            ->where('department', $career->department)
            ->latest()
            ->limit(3)
            ->get();

        $isAuthenticated = Auth::guard('customer')->check();
        $hasApplied = false;
        if (Auth::guard('customer')->check()) {
            $hasApplied = CareerApplication::where('career_id', $career->id)
                ->where('user_id', Auth::guard('customer')->id())
                ->exists();
        }

        return view('customer.careers.show', compact('career', 'related', 'hasApplied', 'isAuthenticated'));
    }

    public function apply(Request $request, Career $career)
    {
        abort_if(!$career->is_active, 404);

        if ($career->is_expired) {
            return back()->with('error', 'Lowongan sudah ditutup.');
        }

        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'phone'            => 'required|string|max:30',
            'address'          => 'nullable|string|max:500',
            'birth_date'       => 'nullable|date|before:today',
            'gender'           => 'nullable|in:male,female',
            'last_education'   => 'nullable|string|max:100',
            'major'            => 'nullable|string|max:100',
            'experience_years' => 'nullable|integer|min:0|max:50',
            'cover_letter'     => 'nullable|string|max:2000',
            'cv_file'          => 'required|file|mimes:pdf,doc,docx|max:5120',
            'portfolio_file'   => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        // 🔥 Simpan CV
        $cvPath = $request->file('cv_file')->store('career/cv', 'public');

        // 🔥 Simpan portfolio (kalau ada)
        $portfolioPath = null;
        if ($request->hasFile('portfolio_file')) {
            $portfolioPath = $request->file('portfolio_file')->store('career/portfolio', 'public');
        }

        // 🔥 Simpan lamaran
        // user_id boleh null kalau guest
        CareerApplication::create([
            'career_id'        => $career->id,
            'user_id'          => Auth::guard('customer')->id(), // null kalau guest
            'full_name'        => $validated['full_name'],
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'address'          => $validated['address'] ?? null,
            'birth_date'       => $validated['birth_date'] ?? null,
            'gender'           => $validated['gender'] ?? null,
            'last_education'   => $validated['last_education'] ?? null,
            'major'            => $validated['major'] ?? null,
            'experience_years' => $validated['experience_years'] ?? 0,
            'cover_letter'     => $validated['cover_letter'] ?? null,
            'cv_file'          => $cvPath,
            'portfolio_file'   => $portfolioPath,
        ]);

        return redirect()
            ->route('customer.careers.show', $career->slug)
            ->with('success', 'Lamaran Anda berhasil dikirim. Kami akan menghubungi Anda segera.');
    }
}