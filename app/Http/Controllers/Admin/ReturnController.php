<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'items.variant', 'user'])
            ->whereIn('return_status', ['pending', 'approved'])
            ->orderBy('return_requested_at', 'desc');

        if ($request->filled('status')) {
            $query->where('return_status', $request->status);
        }

        $orders = $query->paginate(20)->withQueryString();

        $pendingCount = Order::where('return_status', 'pending')->count();
        $approvedCount = Order::where('return_status', 'approved')->count();

        return view('admin.returns.index', compact('orders', 'pendingCount', 'approvedCount'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'items.variant', 'items.product.variants']);

        if (!in_array($order->return_status, ['pending', 'approved'])) {
            return redirect()
                ->route('admin.returns.index')
                ->with('error', 'Pesanan ini tidak memiliki permintaan retur yang valid.');
        }

        return view('admin.returns.show', compact('order'));
    }

    public function restoreStock(Request $request, Order $order)
    {
        $order->loadMissing(['items.variant', 'items.product.variants']);

        if ($order->return_status !== 'approved') {
            return redirect()
                ->route('admin.returns.show', $order)
                ->with('error', 'Retur belum disetujui. Setujui terlebih dahulu.');
        }

        $request->validate([
            'items' => 'nullable|array',
            'items.*' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $selectedItems = $request->input('items', []);
            $hasSelection = !empty($selectedItems) && collect($selectedItems)->filter(fn($q) => $q > 0)->isNotEmpty();

            if ($hasSelection) {
                foreach ($order->items as $item) {
                    $returnQty = $selectedItems[$item->id] ?? 0;
                    if ($returnQty > 0) {
                        if ($item->variant) {
                            $item->variant->addStock(
                                $returnQty,
                                'order_cancelled',
                                "Retur pesanan {$order->order_number} dikembalikan ke stok oleh admin " . Auth::user()->name
                            );
                        } elseif ($item->product) {
                            $firstVariant = $item->product->variants->first();
                            if ($firstVariant) {
                                $firstVariant->addStock(
                                    $returnQty,
                                    'order_cancelled',
                                    "Retur pesanan {$order->order_number} dikembalikan ke stok oleh admin " . Auth::user()->name
                                );
                            }
                        }
                    }
                }
            } else {
                $order->completeReturn();
            }

            $order->update([
                'return_status' => 'completed',
                'return_processed_at' => now(),
                'returned_by_admin_id' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Return stock restored by admin', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'admin_id' => Auth::id(),
                'selected_items' => $hasSelection ? $selectedItems : 'all',
            ]);

            return redirect()
                ->route('admin.returns.index')
                ->with('success', 'Stok retur berhasil dikembalikan. Status retur diperbarui menjadi Selesai.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error restoring return stock:', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('admin.returns.show', $order)
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
