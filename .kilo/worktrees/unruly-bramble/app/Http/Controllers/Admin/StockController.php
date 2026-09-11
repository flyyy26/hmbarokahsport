<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Index - Tampilkan daftar produk dengan stok
     */
    public function index(Request $request)
    {
        $query = Product::with(['variants', 'category'])
            ->where('is_active', true);

        // 🔥 FILTER STOK
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'critical':
                    $query->criticalStock();
                    break;
                case 'low':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->whereHas('variants', function($q) {
                        $q->selectRaw('SUM(stock) as total_stock')
                          ->havingRaw('SUM(stock) = 0');
                    });
                    break;
                case 'in_stock':
                    $query->inStock();
                    break;
            }
        }

        // 🔥 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        $products = $query->paginate(15);

        // 🔥 TAMBAHKAN TOTAL STOK PER PRODUK
        foreach ($products as $product) {
            $product->total_stock = $product->variants->sum('stock');
            $product->stock_status = $product->stock_status;
            $product->stock_status_label = $product->stock_status_label;
            $product->stock_status_color = $product->stock_status_color;
        }

        // 🔥 STATISTIK
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'critical_count' => Product::where('is_active', true)->criticalStock()->count(),
            'low_count' => Product::where('is_active', true)->lowStock()->count(),
            'out_of_stock_count' => Product::where('is_active', true)
                ->whereHas('variants', function($q) {
                    $q->selectRaw('SUM(stock) as total_stock')
                      ->havingRaw('SUM(stock) = 0');
                })->count(),
        ];

        return view('admin.stock.index', compact('products', 'stats'));
    }

    /**
     * Edit stok produk (per varian)
     */
    public function edit(Product $product)
    {
        $product->load(['variants' => function($query) {
            $query->orderBy('price', 'asc');
        }, 'variants.values']);

        // 🔥 TAMBAHKAN HISTORY TERAKHIR PER VARIAN
        foreach ($product->variants as $variant) {
            $variant->last_history = $variant->stockHistories()->latest()->first();
        }

        return view('admin.stock.edit', compact('product'));
    }

    /**
     * Update stok produk (per varian)
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|exists:product_variants,id',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.reason' => 'required|string|in:restock,adjustment,return,damaged,transfer_in,order_cancelled,other', // 🔥 PERBAIKI: tambahkan order_cancelled
            'variants.*.note' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                foreach ($request->variants as $data) {
                    $variant = ProductVariant::findOrFail($data['id']);
                    $oldStock = $variant->stock;
                    $newStock = (int) $data['stock'];
                    
                    if ($oldStock != $newStock) {
                        $quantityChange = $newStock - $oldStock;
                        
                        // Update stok
                        $variant->stock = $newStock;
                        $variant->save();
                        
                        // Catat history
                        $variant->stockHistories()->create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'user_id' => auth()->id(),
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock,
                            'quantity_change' => $quantityChange,
                            'reason' => $data['reason'],
                            'note' => $data['note'] ?? null,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);
                    }
                }
            });

            return redirect()
                ->route('admin.stock.index')
                ->with('success', 'Stok produk berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memperbarui stok: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update stok single variant (AJAX)
     */
    public function updateSingle(Request $request, ProductVariant $variant)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'reason' => 'required|string|in:restock,adjustment,return,damaged,transfer_in,order_cancelled,other', // 🔥 PERBAIKI
            'note' => 'nullable|string|max:255',
        ]);

        try {
            $oldStock = $variant->stock;
            $newStock = (int) $request->stock;
            
            if ($oldStock == $newStock) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stok tidak berubah.',
                ]);
            }

            $quantityChange = $newStock - $oldStock;

            DB::transaction(function () use ($variant, $oldStock, $newStock, $quantityChange, $request) {
                $variant->stock = $newStock;
                $variant->save();

                $variant->stockHistories()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'user_id' => auth()->id(),
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'quantity_change' => $quantityChange,
                    'reason' => $request->reason,
                    'note' => $request->note,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diperbarui.',
                'variant' => [
                    'id' => $variant->id,
                    'stock' => $newStock,
                    'old_stock' => $oldStock,
                    'change' => $quantityChange,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update stok (AJAX)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'exists:product_variants,id',
            'type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|in:restock,adjustment,return,damaged,transfer_in,order_cancelled,other', // 🔥 PERBAIKI
            'note' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);
        $updatedCount = 0;

        try {
            DB::transaction(function () use ($request, $product, &$updatedCount) {
                foreach ($request->variant_ids as $variantId) {
                    $variant = ProductVariant::find($variantId);
                    if (!$variant) continue;

                    $oldStock = $variant->stock;
                    $newStock = $oldStock;

                    if ($request->type === 'add') {
                        $newStock = $oldStock + $request->quantity;
                    } elseif ($request->type === 'subtract') {
                        $newStock = max(0, $oldStock - $request->quantity);
                    } elseif ($request->type === 'set') {
                        $newStock = $request->quantity;
                    }

                    if ($oldStock != $newStock) {
                        $quantityChange = $newStock - $oldStock;

                        $variant->stock = $newStock;
                        $variant->save();

                        $variant->stockHistories()->create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'user_id' => auth()->id(),
                            'old_stock' => $oldStock,
                            'new_stock' => $newStock,
                            'quantity_change' => $quantityChange,
                            'reason' => $request->reason,
                            'note' => $request->note,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                        ]);

                        $updatedCount++;
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => "Berhasil update {$updatedCount} varian.",
                'updated_count' => $updatedCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update stok: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * History stok produk
     */
    public function history(Product $product)
    {
        $histories = StockHistory::with(['variant', 'user'])
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.stock.history', compact('product', 'histories'));
    }

    /**
     * History stok varian (AJAX)
     */
    public function variantHistory(ProductVariant $variant)
    {
        $histories = $variant->stockHistories()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'html' => view('admin.stock._variant_history', compact('histories', 'variant'))->render(),
        ]);
    }
}