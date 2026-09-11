<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = config('services.midtrans.sanitization', true);
        Config::$is3ds = config('services.midtrans.3ds', true);
    }

    /**
     * 🔥 HALAMAN PEMBAYARAN MIDTRANS
     */
    public function pay(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.checkout.success', $order);
        }

        $order->load(['items.product']);

        // 🔥 GENERATE SNAP TOKEN
        $params = $this->buildTransactionParams($order);
        
        try {
            $snapToken = Snap::getSnapToken($params);
            
            // 🔥 LOG UNTUK DEBUG
            Log::info('Midtrans Pay:', [
                'order_id' => $order->order_number,
                'snap_token' => $snapToken,
                'gross_amount' => $order->total,
            ]);
            
            return view('customer.midtrans.pay', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Midtrans Get Snap Token Error:', [
                'message' => $e->getMessage(),
                'order_id' => $order->order_number,
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function refreshToken(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            return response()->json(['success' => false], 403);
        }

        $order->load(['items.product']);
        $params = $this->buildTransactionParams($order);
        
        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * 🔥 BUILD TRANSACTION PARAMS
     */
    private function buildTransactionParams($order)
    {
        $items = [];
        
        // Produk
        foreach ($order->items as $item) {
            $items[] = [
                'id' => $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product_name . ($item->variant_name ? ' - ' . $item->variant_name : ''), 0, 50),
            ];
        }

        // Ongkir
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Biaya Pengiriman',
            ];
        }

        // Diskon
        if ($order->discount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -1 * (int) $order->discount,
                'quantity' => 1,
                'name' => 'Diskon Voucher',
            ];
        }

        $customer = $order->customer;

        return [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $customer->name ?? 'Customer',
                'email' => $customer->email ?? 'customer@email.com',
                'phone' => $order->shipping_phone ?? $customer->phone ?? '08123456789',
                'billing_address' => [
                    'first_name' => $order->shipping_name,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $order->shipping_name,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'callbacks' => [
                'finish' => route('customer.midtrans.finish'),
                'error' => route('customer.midtrans.error'),
            ],
        ];
    }

    /**
     * 🔥 NOTIFICATION HANDLER
     */
    public function notificationHandler(Request $request)
    {
        // 🔥 LOG REQUEST
        Log::info('Midtrans Notification Received:', [
            'method' => $request->method(),
            'payload' => $request->all(),
            'raw_body' => $request->getContent(),
        ]);

        try {
            $notification = new Notification();

            $midtransOrderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type ?? 'unknown';
            $grossAmount = $notification->gross_amount;

            // 🔥 CARI ORDER - LANGSUNG PAKAI ORDER_NUMBER (TANPA SUFFIX)
            $order = Order::where('order_number', $midtransOrderId)->first();

            // 🔥 JIKA TIDAK DITEMUKAN, COBA HAPUS SUFFIX
            if (!$order) {
                $orderNumber = explode('-', $midtransOrderId)[0];
                $order = Order::where('order_number', $orderNumber)->first();
            }

            Log::info('Midtrans Notification Parsed:', [
                'midtrans_order_id' => $midtransOrderId,
                'order_number' => $order?->order_number,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount,
            ]);

            if (!$order) {
                Log::warning('Order not found:', ['midtrans_order_id' => $midtransOrderId]);
                return response()->json(['status' => 'Order not found'], 404);
            }

            // 🔥 CEK GROSS AMOUNT
            if ((int) $grossAmount != (int) $order->total) {
                Log::warning('Gross amount mismatch:', [
                    'midtrans' => $grossAmount,
                    'order' => $order->total,
                ]);
                return response()->json(['status' => 'Amount mismatch'], 400);
            }

            // 🔥 UPDATE ORDER
            if ($transactionStatus == 'settlement' || ($transactionStatus == 'capture' && $fraudStatus == 'accept')) {
                $order->update([
                    'payment_status' => 'paid',
                    'shipping_status' => 'processing',
                    'payment_method' => $paymentType,
                    'paid_at' => now(),
                    'midtrans_status' => $transactionStatus,
                ]);

                Log::info('Payment success updated:', [
                    'order_id' => $order->order_number,
                    'payment_status' => $order->payment_status,
                ]);
            } else {
                $order->update([
                    'midtrans_status' => $transactionStatus,
                    'payment_method' => $paymentType,
                ]);
            }

            return response()->json(['status' => 'OK'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🔥 HANDLE PAYMENT SUCCESS
     */
    private function handlePaymentSuccess($order, $paymentType)
    {
        // 🔥 UPDATE ORDER
        $order->update([
            'payment_status' => 'paid',
            'shipping_status' => 'processing',
            'payment_method' => $paymentType,
            'paid_at' => now(),
            'midtrans_status' => 'settlement',
        ]);

        // 🔥 REFRESH ORDER
        $order->refresh();

        Log::info('Payment successful:', [
            'order_id' => $order->order_number,
            'payment_type' => $paymentType,
            'payment_status' => $order->payment_status,
            'total' => $order->total,
        ]);
    }

    public function finish(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        $status = $request->query('status');
        $paymentType = $request->query('payment_type', 'unknown');
        
        Log::info('Midtrans Finish Page:', [
            'order_id' => $midtransOrderId,
            'status' => $status,
            'payment_type' => $paymentType,
        ]);
        
        if (!$midtransOrderId) {
            return redirect()->route('customer.home');
        }

        // 🔥 CARI ORDER
        $order = Order::where('order_number', $midtransOrderId)->first();
        
        if (!$order) {
            $orderNumber = explode('-', $midtransOrderId)[0];
            $order = Order::where('order_number', $orderNumber)->first();
        }

        if (!$order) {
            return redirect()->route('customer.home')->with('error', 'Order tidak ditemukan.');
        }

        // 🔥 CEK STATUS DI DATABASE
        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.checkout.success', $order)
                ->with('success', 'Pembayaran berhasil!');
        }

        // 🔥 CEK STATUS KE MIDTRANS
        try {
            $statusResponse = $this->checkTransactionStatus($midtransOrderId);
            
            Log::info('Midtrans Status Check:', [
                'order_id' => $midtransOrderId,
                'response' => $statusResponse,
            ]);
            
            $transactionStatus = $statusResponse['transaction_status'] ?? null;
            
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                // 🔥 UPDATE MANUAL
                $order->update([
                    'payment_status' => 'paid',
                    'shipping_status' => 'processing',
                    'payment_method' => $statusResponse['payment_type'] ?? 'unknown',
                    'paid_at' => now(),
                    'midtrans_status' => $transactionStatus,
                ]);
                
                return redirect()->route('customer.checkout.success', $order)
                    ->with('success', 'Pembayaran berhasil!');
            }
            
            if ($transactionStatus === 'pending') {
                return redirect()->route('customer.midtrans.pay', $order)
                    ->with('info', 'Pembayaran sedang diproses.');
            }
            
        } catch (\Exception $e) {
            Log::error('Check status error:', [
                'order_id' => $midtransOrderId,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('customer.midtrans.pay', $order)
            ->with('info', 'Silakan selesaikan pembayaran Anda.');
    }

    public function checkStatus(Order $order)
    {
        if ($order->user_id !== Auth::guard('customer')->id()) {
            return response()->json(['paid' => false], 403);
        }

        return response()->json([
            'paid' => $order->payment_status === 'paid',
            'status' => $order->payment_status
        ]);
    }


    /**
     * 🔥 ERROR PAGE
     */
    public function error(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        $orderNumber = explode('-', $midtransOrderId)[0] ?? '';
        
        $order = Order::where('order_number', $orderNumber)->first();

        if ($order) {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
        }

        return redirect()->route('customer.home')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    /**
     * 🔥 CHECK TRANSACTION STATUS
     */
    private function checkTransactionStatus($orderId)
    {
        $url = config('services.midtrans.is_production') 
            ? 'https://api.midtrans.com/v2/' . $orderId . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . $orderId . '/status';
        
        $serverKey = config('services.midtrans.server_key');
        
        $response = Http::withBasicAuth($serverKey, '')
            ->get($url);
        
        return $response->json();
    }
}