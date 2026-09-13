<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::withCount('applications');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('department', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $careers = $query->latest()->paginate(10);

        return view('admin.careers.index', compact('careers'));
    }

    public function create()
    {
        return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCareer($request);
        $validated['slug'] = $this->generateSlug($validated['title']);

        // 🔥 PAKSA SET BOOLEAN (override apapun yang dari validate)
        $validated['is_active']   = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['show_salary'] = $request->boolean('show_salary');

        if ($request->filled('published_at')) {
            $validated['published_at'] = $request->published_at;
        } else {
            $validated['published_at'] = now();
        }

        Career::create($validated);

        return redirect()
            ->route('admin.careers.index')
            ->with('success', 'Lowongan berhasil ditambahkan.');
    }

    public function update(Request $request, Career $career)
    {
        $validated = $this->validateCareer($request);

        if ($career->title !== $validated['title']) {
            $validated['slug'] = $this->generateSlug($validated['title'], $career->id);
        }

        // 🔥 PAKSA SET BOOLEAN
        $validated['is_active']   = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['show_salary'] = $request->boolean('show_salary');

        $career->update($validated);

        return redirect()
            ->route('admin.careers.index')
            ->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function destroy(Career $career)
    {
        // hapus file pelamar juga
        foreach ($career->applications as $app) {
            if ($app->cv_file && Storage::disk('public')->exists($app->cv_file)) {
                Storage::disk('public')->delete($app->cv_file);
            }
            if ($app->portfolio_file && Storage::disk('public')->exists($app->portfolio_file)) {
                Storage::disk('public')->delete($app->portfolio_file);
            }
        }

        $career->delete();

        return back()->with('success', 'Lowongan berhasil dihapus.');
    }

    // ============================================
    // APPLICATIONS
    // ============================================
    public function applications(Career $career, Request $request)
    {
        // 🔥 Tandai semua pelamar di lowongan ini sebagai sudah dibaca
        $career->applications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = $career->applications()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(20);

        return view('admin.careers.applications', compact('career', 'applications'));
    }

    public function showApplication(Career $career, CareerApplication $application)
    {
        abort_if($application->career_id !== $career->id, 404);

        // 🔥 Tandai pelamar ini sudah dibaca
        if (is_null($application->read_at)) {
            $application->update(['read_at' => now()]);
        }

        return view('admin.careers.application-show', compact('career', 'application'));
    }

    public function updateApplication(Request $request, Career $career, CareerApplication $application)
    {
        abort_if($application->career_id !== $career->id, 404);

        $validated = $request->validate([
            'status'      => 'required|in:pending,reviewed,shortlisted,interview,hired,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $validated['reviewed_at'] = now();

        $application->update($validated);

        return back()->with('success', 'Status pelamar berhasil diperbarui.');
    }

    public function destroyApplication(Career $career, CareerApplication $application)
    {
        abort_if($application->career_id !== $career->id, 404);

        if ($application->cv_file && Storage::disk('public')->exists($application->cv_file)) {
            Storage::disk('public')->delete($application->cv_file);
        }
        if ($application->portfolio_file && Storage::disk('public')->exists($application->portfolio_file)) {
            Storage::disk('public')->delete($application->portfolio_file);
        }

        $application->delete();

        return back()->with('success', 'Lamaran berhasil dihapus.');
    }

    // ============================================
    // HELPERS
    // ============================================
    protected function validateCareer(Request $request): array
    {
        return $request->validate([
            'title'              => 'required|string|max:255',
            'department'         => 'nullable|string|max:100',
            'location'           => 'nullable|string|max:100',
            'type'               => 'required|in:full_time,part_time,contract,internship,freelance',
            'level'              => 'required|in:staff,senior,supervisor,manager,director',
            'short_description'  => 'nullable|string|max:500',
            'description'        => 'nullable|string',
            'requirements'       => 'nullable|string',
            'benefits'           => 'nullable|string',
            'salary_min'         => 'nullable|numeric|min:0',
            'salary_max'         => 'nullable|numeric|min:0|gte:salary_min',
            'show_salary'        => 'nullable|boolean',
            'deadline'           => 'nullable|date',
            'quota'              => 'nullable|integer|min:1',
            'is_active'          => 'nullable|boolean',
            'is_featured'        => 'nullable|boolean',
            'published_at'       => 'nullable|date',
        ]);
    }

    protected function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 1;

        while (
            Career::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }
}