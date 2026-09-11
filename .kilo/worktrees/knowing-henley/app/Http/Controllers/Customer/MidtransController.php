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
        if ($order->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.checkout.success', $order);
        }

        $order->load(['items.product']);

        // 🔥 GENERATE SNAP TOKEN SETIAP KALI HALAMAN DIMUAT
        // Ini memastikan token selalu fresh
        $params = $this->buildTransactionParams($order);
        
        try {
            $snapToken = Snap::getSnapToken($params);
            
            // 🔥 SIMPAN SNAP TOKEN KE SESSION ATAU DATABASE (OPSIONAL)
            // Untuk memastikan token yang sama bisa digunakan ulang
            
            return view('customer.midtrans.pay', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            Log::error('Midtrans Get Snap Token Error:', [
                'message' => $e->getMessage(),
                'order_id' => $order->order_number
            ]);
            
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function refreshToken(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
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
                'order_id' => $order->order_number . '-' . time(), 
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
        try {
            $notification = new Notification();

            $midtransOrderId = $notification->order_id;

            $orderNumber = explode('-', $midtransOrderId)[0];
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type ?? 'unknown';
            $grossAmount = $notification->gross_amount;

            Log::info('Midtrans Notification:', [
                'order_id' => $orderNumber,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
            ]);

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('Order not found:', ['order_id' => $orderNumber]);
                return response()->json(['status' => 'Order not found'], 404);
            }

            // Jika sudah diproses, skip
            if (in_array($order->payment_status, ['paid', 'settlement'])) {
                Log::info('Order already processed:', ['order_id' => $orderNumber]);
                return response()->json(['status' => 'Order already processed'], 200);
            }

            switch ($transactionStatus) {
                case 'capture':
                    if ($fraudStatus === 'accept') {
                        $this->handlePaymentSuccess($order, $paymentType);
                    }
                    break;
                    
                case 'settlement':
                    $this->handlePaymentSuccess($order, $paymentType);
                    break;
                    
                case 'pending':
                    $order->update([
                        'payment_status' => 'pending',
                        'payment_method' => $paymentType,
                        'midtrans_status' => $transactionStatus,
                    ]);
                    break;
                    
                case 'deny':
                    $order->update([
                        'payment_status' => 'failed',
                        'midtrans_status' => $transactionStatus,
                    ]);
                    break;
                    
                case 'expire':
                    $order->update([
                        'payment_status' => 'expired',
                        'midtrans_status' => $transactionStatus,
                    ]);
                    break;
                    
                case 'cancel':
                    $order->update([
                        'payment_status' => 'canceled',
                        'midtrans_status' => $transactionStatus,
                    ]);
                    break;
                    
                default:
                    $order->update([
                        'midtrans_status' => $transactionStatus,
                    ]);
                    break;
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'Error'], 500);
        }
    }

    /**
     * 🔥 HANDLE PAYMENT SUCCESS
     */
    private function handlePaymentSuccess($order, $paymentType)
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'payment_method' => $paymentType,
            'paid_at' => now(),
            'midtrans_status' => 'settlement',
        ]);

        // Update voucher usage
        if ($order->discount > 0) {
            $voucherUsage = VoucherUsage::where('order_id', $order->id)->first();
            if ($voucherUsage && $voucherUsage->voucher) {
                // Sudah di-increment di CheckoutController, skip
            }
        }

        Log::info('Payment successful:', [
            'order_id' => $order->order_number,
            'payment_type' => $paymentType
        ]);
    }

    /**
     * 🔥 FINISH PAGE
     */
    public function finish(Request $request)
    {
        // 🔥 AMBIL ID DARI MIDTRANS (BER-SUFFIX WAKTU)
        $midtransOrderId = $request->query('order_id');
        
        if (!$midtransOrderId) {
            return redirect()->route('customer.home');
        }

        // 🔥 AMBIL ORDER NUMBER ASLI
        $orderNumber = explode('-', $midtransOrderId)[0];
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect()->route('customer.home')->with('error', 'Order tidak ditemukan.');
        }

        // 🔥 CEK APAKAH ORDER SUDAH PAID
        if ($order->payment_status === 'paid') {
            return redirect()->route('customer.checkout.success', $order)
                ->with('success', 'Pembayaran berhasil!');
        }

        // 🔥 CEK STATUS TRANSAKSI DARI MIDTRANS (Gunakan ID yang ada suffix-nya!)
        try {
            $status = $this->checkTransactionStatus($midtransOrderId);
            
            if ($status && in_array($status['transaction_status'] ?? '', ['settlement', 'capture'])) {
                $this->handlePaymentSuccess($order, $status['payment_type'] ?? 'unknown');
                return redirect()->route('customer.checkout.success', $order)
                    ->with('success', 'Pembayaran berhasil!');
            }
            
            if ($status && in_array($status['transaction_status'] ?? '', ['pending', 'challenge'])) {
                return redirect()->route('customer.midtrans.pay', $order)
                    ->with('info', 'Pembayaran sedang diproses. Silakan selesaikan pembayaran Anda.');
            }
            
        } catch (\Exception $e) {
            Log::error('Check transaction status error:', [
                'order_id' => $midtransOrderId,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('customer.midtrans.pay', $order)
            ->with('info', 'Silakan selesaikan pembayaran Anda.');
    }

    public function checkStatus(Order $order)
    {
        if ($order->customer_id !== Auth::guard('customer')->id()) {
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