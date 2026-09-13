<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfflineOrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Product::where('is_active', true)
            ->with(['variants' => function($q) {
                $q->where('is_active', true);
            }, 'variants.values']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderBy('name')
            ->paginate(12)
            ->appends($request->except('page'));

        $categories = \App\Models\Category::where('is_active', true)
            ->withCount(['products' => function($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();

        return view('admin.orders.offline', compact('products', 'search', 'categories', 'categoryId'));
    }

    public function createOrder(Request $request)
    {
        $items = $request->input('items');
        if (is_string($items)) {
            $items = json_decode($items, true) ?? [];
            $request->merge(['items' => $items]);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cash,transfer,qris',
            'transaction_discount' => 'nullable|numeric|min:0',
            'transaction_discount_type' => 'nullable|in:nominal,percentage',
        ]);

        $printType = $request->input('print_type', 'nota');

        return DB::transaction(function () use ($validated, $printType) {
            $now = now();
            $orderNumber = 'OFF-' . strtoupper(Str::random(6)) . '-' . $now->format('YmdHis');

            $subtotal = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $discount = $item['discount'] ?? 0;
                $lineSubtotal = $item['price'] * $item['quantity'];
                $lineDiscount = $discount * $item['quantity'];

                $subtotal += $lineSubtotal;
                $totalDiscount += $lineDiscount;
            }

            $discountableBase = $subtotal - $totalDiscount;

            $transactionDiscountValue = (float) ($validated['transaction_discount'] ?? 0);
            $transactionDiscountType = $validated['transaction_discount_type'] ?? 'nominal';

            if ($transactionDiscountType === 'percentage') {
                $transactionDiscountValue = min($transactionDiscountValue, 100);
                $transactionDiscount = $discountableBase * $transactionDiscountValue / 100;
            } else {
                $transactionDiscount = min($transactionDiscountValue, $discountableBase);
            }

            $total = $discountableBase - $transactionDiscount;

            $order = OfflineOrder::create([
                'order_number' => $orderNumber,
                'status' => 'delivered',
                'payment_status' => 'paid',
                'shipping_status' => 'delivered',
                'customer_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                'customer_phone' => $validated['customer_phone'] ?? '-',
                'customer_address' => $validated['customer_address'] ?? null,
                'shipping_name' => $validated['customer_name'] ?? 'Walk-in Customer',
                'shipping_phone' => $validated['customer_phone'] ?? '-',
                'shipping_address' => $validated['customer_address'] ?? '-',
                'shipping_city' => '-',
                'shipping_province' => '-',
                'shipping_district' => null,
                'shipping_subdistrict' => null,
                'shipping_postal_code' => '0',
                'subtotal' => $subtotal,
                'shipping_cost' => 0,
                'original_shipping_cost' => 0,
                'transaction_discount' => $transactionDiscount,
                'transaction_discount_type' => $transactionDiscountType,
                'discount' => $totalDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'],
                'paid_at' => $now,
                'delivered_at' => $now,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $variantName = '';
                $sku = '';

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::with('values')->find($item['variant_id']);
                    if ($variant) {
                        if ($variant->stock < $item['quantity']) {
                            throw new \Exception("Stok tidak mencukupi untuk varian: {$variant->option_combination}. Stok tersedia: {$variant->stock}");
                        }
                        $variantName = $variant->option_combination;
                        $sku = $variant->sku;
                        $variant->update(['stock' => max(0, $variant->stock - $item['quantity'])]);
                    }
                } else {
                    $firstVariant = $product->variants->first();
                    if ($firstVariant) {
                        if ($firstVariant->stock < $item['quantity']) {
                            throw new \Exception("Stok tidak mencukupi untuk produk: {$product->name}. Stok tersedia: {$firstVariant->stock}");
                        }
                        $sku = $firstVariant->sku;
                    }
                }

                $lineSubtotal = $item['price'] * $item['quantity'];

                OfflineOrderItem::create([
                    'offline_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $product ? $product->name : '',
                    'variant_name' => $variantName,
                    'sku' => $sku,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineSubtotal,
                    'variant_attributes' => null,
                ]);
            }

            if ($printType === 'faktur') {
                return redirect()->route('admin.orders.offline.faktur', $order);
            }

            return redirect()->route('admin.orders.offline.receipt', $order);
        });
    }

    public function printReceipt(OfflineOrder $order)
    {
        $order->load(['items', 'items.variant']);
        $setting = Setting::first();

        $pdf = Pdf::loadView('admin.orders.receipt', compact('order', 'setting'));
        $pdf->setPaper('a6', 'portrait');

        return $pdf->stream('nota-' . $order->order_number . '.pdf');
    }

    public function printFaktur(OfflineOrder $order)
    {
        $order->load(['items', 'items.variant']);
        $setting = Setting::first();

        $pdf = Pdf::loadView('admin.orders.faktur', compact('order', 'setting'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('faktur-' . $order->order_number . '.pdf');
    }
}
