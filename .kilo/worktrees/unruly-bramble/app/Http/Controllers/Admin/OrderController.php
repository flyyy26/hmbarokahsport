<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // ============================================
    // INDEX
    // ============================================

    public function index(Request $request)
    {
        $query = Order::with(['customer', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment_status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Status counts
        $statusCounts = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    // ============================================
    // SHOW
    // ============================================

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'items.variant']);
        return view('admin.orders.show', compact('order'));
    }

    // ============================================
    // UPDATE STATUS
    // ============================================

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        // Jika status berubah menjadi shipped, set shipped_at
        if ($validated['status'] === 'shipped' && $order->status !== 'shipped') {
            $order->shipped_at = now();
        }

        // Jika status berubah menjadi delivered, set delivered_at
        if ($validated['status'] === 'delivered' && $order->status !== 'delivered') {
            $order->delivered_at = now();
        }

        // Jika status berubah menjadi cancelled, set cancelled_at
        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            $order->cancelled_at = now();
            
            // Restore stock jika dibatalkan
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);
                }
            }
        }

        $order->status = $validated['status'];
        
        if ($request->filled('admin_notes')) {
            $order->admin_notes = $validated['admin_notes'];
        }

        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    // ============================================
    // UPDATE PAYMENT STATUS
    // ============================================

    public function updatePayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid,failed,refunded',
        ]);

        // Jika status berubah menjadi paid, set paid_at
        if ($validated['payment_status'] === 'paid' && $order->payment_status !== 'paid') {
            $order->paid_at = now();
        }

        $order->payment_status = $validated['payment_status'];
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    // ============================================
    // UPDATE SHIPPING
    // ============================================

    public function updateShipping(Request $request, Order $order)
    {
        $validated = $request->validate([
            'courier' => 'nullable|string|max:100',
            'service' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_status' => 'required|in:pending,processing,shipped,delivered',
        ]);

        // Jika shipping_status menjadi shipped
        if ($validated['shipping_status'] === 'shipped' && $order->shipping_status !== 'shipped') {
            $order->shipped_at = now();
            // Update main status juga ke shipped
            if ($order->status === 'processing') {
                $order->status = 'shipped';
            }
        }

        if ($validated['shipping_status'] === 'delivered' && $order->shipping_status !== 'delivered') {
            $order->delivered_at = now();
            if ($order->status === 'shipped') {
                $order->status = 'delivered';
            }
        }

        $order->courier = $validated['courier'];
        $order->service = $validated['service'];
        $order->tracking_number = $validated['tracking_number'];
        $order->shipping_status = $validated['shipping_status'];
        $order->save();

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Informasi pengiriman berhasil diperbarui.');
    }

    // ============================================
    // PRINT INVOICE
    // ============================================

    public function invoice(Order $order)
    {
        $order->load(['customer', 'items.product', 'items.variant']);
        return view('admin.orders.invoice', compact('order'));
    }
}