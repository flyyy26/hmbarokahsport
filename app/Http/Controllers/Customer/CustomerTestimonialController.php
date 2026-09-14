<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Testimonial;
use App\Models\TestimonialImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Services\ImageOptimizer;

class CustomerTestimonialController extends Controller
{
    protected ImageOptimizer $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }
    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $tab = $request->query('tab', 'all');

        if ($tab === 'all') {
            $orders = Order::where('user_id', $customer->id)
                ->whereDoesntHave('items.product.testimonials', function ($q) use ($customer) {
                    $q->where('user_id', $customer->id);
                })
                ->where(function ($q) {
                    $q->where('shipping_status', 'delivered')->orWhereNotNull('delivered_at');
                })
                ->with(['items.product', 'items.variant', 'items.variant.values', 'items.product.images'])
                ->orderBy('delivered_at', 'desc')
                ->get();
        } else {
            $orders = Order::where('user_id', $customer->id)
                ->whereHas('items.product.testimonials', function ($q) use ($customer) {
                    $q->where('user_id', $customer->id);
                })
                ->where(function ($q) {
                    $q->where('shipping_status', 'delivered')->orWhereNotNull('delivered_at');
                })
                ->with(['items.product', 'items.variant', 'items.variant.values', 'items.product.images'])
                ->orderBy('delivered_at', 'desc')
                ->get();
        }

        return view('customer.account.testimonials', [
            'orders' => $orders,
            'activeTab' => $tab,
        ]);
    }

    public function create(Order $order)
    {
        $customer = Auth::guard('customer')->user();

        if ($order->user_id !== $customer->id) {
            return redirect()->route('customer.orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        if (!in_array($order->shipping_status, ['delivered']) && !$order->delivered_at) {
            return redirect()->route('customer.orders', ['tab' => 'completed'])->with('error', 'Testimonial hanya bisa diberikan untuk pesanan yang sudah selesai.');
        }

        $order->load(['items.product', 'items.variant', 'items.variant.values', 'items.product.images']);

        return response()->json([
            'html' => view('customer.testimonial._modal', compact('order', 'customer'))->render(),
        ]);
    }

    public function store(Request $request, Order $order)
    {
        $customer = Auth::guard('customer')->user();

        if ($order->user_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        if (!in_array($order->shipping_status, ['delivered']) && !$order->delivered_at) {
            return response()->json([
                'success' => false,
                'message' => 'Testimonial hanya bisa diberikan untuk pesanan yang sudah selesai.',
            ], 422);
        }

        $order->loadMissing('items.product', 'items.variant');

        $firstItem = $order->items->first();
        if (!$firstItem) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak memiliki produk.',
            ], 422);
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'testimonial' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validated['product_id'] = $firstItem->product_id;
        $validated['product_variant_id'] = $firstItem->product_variant_id;
        $validated['user_id'] = $customer->id;
        $validated['is_active'] = true;
        $validated['is_verified_purchase'] = true;
        $validated['published_at'] = now();

        $uploadedFiles = [];

        try {
            DB::transaction(function () use ($request, $validated, &$uploadedFiles) {
                $testimonial = Testimonial::create($validated);

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        if (!$image->isValid()) continue;

                        // 🔥 CONVERT KE WEBP
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

            return response()->json([
                'success' => true,
                'message' => 'Testimonial berhasil dikirim. Terima kasih atas ulasan Anda!',
            ]);

        } catch (Throwable $e) {
            foreach ($uploadedFiles as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim testimonial: ' . $e->getMessage(),
            ], 500);
        }
    }
}
