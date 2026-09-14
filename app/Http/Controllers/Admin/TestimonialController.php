<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Testimonial;
use App\Models\TestimonialImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Services\ImageOptimizer;

class TestimonialController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
    public function index(Request $request)
    {
        $query = Testimonial::with(['product', 'variant', 'user', 'images']);

        if ($request->filled('product_id') && $request->product_id !== '') {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('verified') && $request->verified !== '') {
            $query->where('is_verified_purchase', $request->verified === 'verified');
        }

        $testimonials = $query->ordered()->paginate(15)->appends($request->except('page'));

        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.testimonials.index', compact('testimonials', 'products'));
    }

    public function create()
    {
        $products = Product::with([
            'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            },
            'variants.values',
            'images' => function ($q) {
                $q->orderBy('sort_order');
            },
        ])->where('is_active', true)->orderBy('name')->get();

        $productsData = $products->keyBy('id')->map(function ($p) {
            return $p->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'option_combination' => $v->option_combination ?: 'Default',
                    'price' => $v->price,
                ];
            });
        });

        return view('admin.testimonials.create', compact('products', 'productsData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'testimonial' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
            'is_verified_purchase' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_verified_purchase'] = $request->boolean('is_verified_purchase', false);

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $uploadedFiles = [];

        try {
            DB::transaction(function () use ($request, $validated, &$uploadedFiles) {
                $testimonial = Testimonial::create($validated);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        if (!$image->isValid()) continue;

                        $path = $this->imageOptimizer->convertToWebp(
                            file: $image,
                            folder: 'testimonials',
                            maxWidth: 800,
                            quality: 82
                        );
                        $uploadedFiles[] = $path;

                        $testimonial->images()->create([
                            'image' => $path,
                            'sort_order' => $index,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('admin.testimonials.index')
                ->with('success', 'Testimonial berhasil ditambahkan.');

        } catch (Throwable $e) {
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan testimonial: ' . $e->getMessage());
        }
    }

    public function show(Testimonial $testimonial)
    {
        $testimonial->load(['product.images', 'variant', 'user', 'images']);

        return view('admin.testimonials.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial)
    {
        $testimonial->load(['product', 'variant', 'images']);

        $products = Product::with([
            'variants' => function ($q) {
                $q->where('is_active', true)->orderBy('price');
            },
            'variants.values',
            'images' => function ($q) {
                $q->orderBy('sort_order');
            },
        ])->where('is_active', true)->orderBy('name')->get();

        $productsData = $products->keyBy('id')->map(function ($p) {
            return $p->variants->map(function ($v) {
                return [
                    'id' => $v->id,
                    'option_combination' => $v->option_combination ?: 'Default',
                    'price' => $v->price,
                ];
            });
        });

        return view('admin.testimonials.edit', compact('testimonial', 'products', 'productsData'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'exists:product_variants,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'testimonial' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'existing_images' => ['nullable', 'array'],
            'existing_images.*' => ['integer', 'exists:testimonial_images,id'],
            'is_active' => ['nullable', 'boolean'],
            'is_verified_purchase' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_verified_purchase'] = $request->boolean('is_verified_purchase', false);

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $uploadedFiles = [];

        try {
            DB::transaction(function () use ($request, $validated, $testimonial, &$uploadedFiles) {
                $testimonial->update($validated);

                $keepImageIds = collect($request->input('existing_images', []))
                    ->map(fn ($id) => (int) $id)
                    ->toArray();

                $oldImages = $testimonial->images()
                    ->when(!empty($keepImageIds), function ($query) use ($keepImageIds) {
                        return $query->whereNotIn('id', $keepImageIds);
                    })
                    ->get();

                foreach ($oldImages as $oldImage) {
                    $this->imageOptimizer->delete($oldImage->image);
                    $oldImage->delete();
                }

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        if (!$image->isValid()) continue;

                        $path = $this->imageOptimizer->convertToWebp(
                            file: $image,
                            folder: 'testimonials',
                            maxWidth: 800,
                            quality: 82
                        );
                        $uploadedFiles[] = $path;

                        $testimonial->images()->create([
                            'image' => $path,
                            'sort_order' => $index,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('admin.testimonials.index')
                ->with('success', 'Testimonial berhasil diperbarui.');

        } catch (Throwable $e) {
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui testimonial: ' . $e->getMessage());
        }
    }

    public function destroy(Testimonial $testimonial)
    {
        try {
            DB::transaction(function () use ($testimonial) {
                if ($testimonial->images) {
                    foreach ($testimonial->images as $image) {
                        $this->imageOptimizer->delete($image->image);
                    }
                }

                $testimonial->delete();
            });

            return redirect()
                ->route('admin.testimonials.index')
                ->with('success', 'Testimonial berhasil dihapus.');

        } catch (Throwable $e) {
            return back()
                ->with('error', 'Gagal menghapus testimonial: ' . $e->getMessage());
        }
    }

    public function destroyImage(Testimonial $testimonial, $image)
    {
        $testimonialImage = $testimonial->images()->findOrFail($image);

        $this->imageOptimizer->delete($testimonialImage->image);

        $testimonialImage->delete();

        return back()->with('success', 'Gambar testimonial berhasil dihapus.');
    }


    public function toggle(Testimonial $testimonial)
    {
        try {
            $testimonial->update(['is_active' => !$testimonial->is_active]);
            $status = $testimonial->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()
                ->route('admin.testimonials.index')
                ->with('success', "Testimonial berhasil {$status}.");
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal mengubah status testimonial.');
        }
    }

    public function verify(Request $request, Testimonial $testimonial)
    {
        try {
            $testimonial->update(['is_verified_purchase' => !$testimonial->is_verified_purchase]);
            $status = $testimonial->is_verified_purchase ? 'diveriifikasi' : 'dibatalkan verifikasi';
            return redirect()
                ->route('admin.testimonials.index')
                ->with('success', "Testimonial berhasil {$status}.");
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal memverifikasi testimonial.');
        }
    }
}
