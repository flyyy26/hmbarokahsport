@extends('layouts.customer')

@section('title', 'Pembayaran - ' . config('app.name'))

@section('content')
<style>
    .payment-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 1.5rem;
    }
    .payment-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 2rem rgba(0,0,0,0.1);
        padding: 2rem;
    }
    .payment-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .payment-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
    }
    .payment-header p {
        color: #94a3b8;
        font-size: 0.9rem;
    }
    .payment-header .order-id {
        display: inline-block;
        background: #f1f5f9;
        padding: 0.2rem 1rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
        color: #475569;
        margin-top: 0.3rem;
    }
    .order-summary {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .order-summary .row {
        display: flex;
        justify-content: space-between;
        padding: 0.3rem 0;
        font-size: 0.9rem;
    }
    .order-summary .total {
        font-weight: 700;
        font-size: 1.1rem;
        border-top: 1px solid #e2e8f0;
        padding-top: 0.5rem;
        margin-top: 0.3rem;
    }
    #midtrans-button {
        width: 100%;
        padding: 0.8rem;
        background: #076694;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    #midtrans-button:hover {
        background: #054b6e;
    }
    #midtrans-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    #midtrans-button .btn-icon {
        margin-right: 0.5rem;
    }
    /* 🔥 STYLE UNTUK TOMBOL GANTI METODE */
    #midtrans-button.change-method {
        background: #f59e0b;
    }
    #midtrans-button.change-method:hover {
        background: #d97706;
    }
    .btn-back {
        display: block;
        text-align: center;
        color: #94a3b8;
        text-decoration: none;
        margin-top: 1rem;
        font-size: 0.9rem;
    }
    .btn-back:hover {
        color: #0f172a;
    }
    .loading-spinner {
        display: inline-block;
        width: 1.2rem;
        height: 1.2rem;
        border: 2px solid #fff;
        border-radius: 50%;
        border-top-color: transparent;
        animation: spin 0.8s linear infinite;
        vertical-align: middle;
        margin-right: 0.5rem;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* 🔥 ALERT */
    .alert-info {
        background: #eff6ff;
        border: 1px solid #93c5fd;
        color: #1d4ed8;
        padding: 0.8rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-info iconify-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #16a34a;
        padding: 0.8rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        padding: 0.8rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-error iconify-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .alert-warning {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        color: #92400e;
        padding: 0.8rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-warning iconify-icon {
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* 🔥 STYLE UNTUK TOMBOL ACTION */
    .action-buttons-pay {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    #change-method-button {
        width: 100%;
        padding: 0.8rem;
        background: #f59e0b;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    #change-method-button:hover {
        background: #d97706;
    }
    #change-method-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    /* 🔥 PAYMENT METHODS LIST */
    .payment-methods-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .payment-method-item {
        background: #f1f5f9;
        padding: 0.6rem 0.3rem;
        border-radius: 0.4rem;
        text-align: center;
        font-size: 0.7rem;
        color: #475569;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    .payment-method-item:hover {
        border-color: #076694;
        background: #eff6ff;
    }
    .payment-method-item iconify-icon {
        font-size: 1.3rem;
        display: block;
        margin-bottom: 0.2rem;
    }
    .payment-method-item small {
        display: block;
        font-size: 0.55rem;
        color: #94a3b8;
        margin-top: 0.1rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.2rem 0.8rem;
        border-radius: 0.3rem;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    .status-badge.unpaid {
        background: #fef3c7;
        color: #92400e;
    }
    .status-badge.paid {
        background: #d1fae5;
        color: #065f46;
    }
    .status-badge.pending {
        background: #dbeafe;
        color: #1e40af;
    }
</style>

<div class="payment-container">
    <div class="payment-card">
        <div class="payment-header">
            <h2>Selesaikan Pembayaran</h2>
            <p>Pilih metode pembayaran yang tersedia</p>
            <span class="order-id">Order: {{ $order->order_number }}</span>
            <br>
            <span class="status-badge {{ $order->payment_status }}">
                {{ $order->payment_status === 'unpaid' ? 'Belum Dibayar' : ucfirst($order->payment_status) }}
            </span>
        </div>

        {{-- 🔥 ALERT --}}
        <div id="alert-container">
            @if(session('info'))
            <div class="alert-info" data-session="true">
                <iconify-icon icon="mdi:information-outline"></iconify-icon>
                <span>{{ session('info') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error" data-session="true">
                <iconify-icon icon="mdi:alert-circle-outline"></iconify-icon>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($order->payment_status === 'paid')
            <div class="alert-success" data-session="true">
                <iconify-icon icon="mdi:check-circle-outline"></iconify-icon>
                <span>Pembayaran sudah berhasil! <a href="{{ route('customer.checkout.success', $order) }}" style="color:#16a34a;font-weight:600;">Lihat detail pesanan</a></span>
            </div>
            @endif
        </div>

        <div class="order-summary">
            <div class="row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->shipping_cost > 0)
            <div class="row">
                <span>Ongkir</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->discount > 0)
            <div class="row" style="color:#16a34a;">
                <span>Diskon</span>
                <span>-Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="row total">
                <span>Total</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- 🔥 BUTTON --}}
        <div class="action-buttons-pay">
            <button id="midtrans-button" type="button">
                <span id="btn-text">
                    Bayar Sekarang
                </span>
            </button>
            
            <button id="change-method-button" type="button" style="display: none;">
                Ganti Metode Pembayaran
            </button>
        </div>

        <a href="{{ route('customer.orders.show', $order) }}" class="btn-back">
            ← Kembali ke Detail Pesanan
        </a>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 VARIABEL STATE
    let currentSnapToken = @json($snapToken);
    const orderId = @json($order->order_number);
    const paymentStatus = @json($order->payment_status);

    // 🔥 ELEMEN
    const btn = document.getElementById('midtrans-button');
    const btnText = document.getElementById('btn-text');
    const btnChange = document.getElementById('change-method-button');
    const alertContainer = document.getElementById('alert-container');

    if (paymentStatus === 'paid') {
        btn.disabled = true;
        btnText.innerHTML = 'Pembayaran Selesai';
        return;
    }

    let isProcessing = false;

    // 🔥 FUNGSI UPDATE TAMPILAN TOMBOL
    function updateButton(state) {
        if (state === 'loading') {
            btn.disabled = true;
            btnChange.disabled = true;
            btnText.innerHTML = '<span class="loading-spinner"></span> Menghubungkan...';
        } else if (state === 'pending' || state === 'change') {
            btn.disabled = false;
            btnChange.disabled = false;
            btnText.innerHTML = 'Lanjutkan Pembayaran';
            btnChange.style.display = 'block'; // Tampilkan tombol ganti metode
        } else {
            btn.disabled = false;
            btnChange.disabled = false;
            btnText.innerHTML = 'Bayar Sekarang';
            btnChange.style.display = 'none'; // Sembunyikan tombol ganti metode
        }
    }

    function showAlert(message, type = 'info') {
        // ... (Fungsi showAlert biarkan sama seperti kodemu sebelumnya) ...
        const types = {
            info: { className: 'alert-info', icon: 'mdi:information-outline' },
            error: { className: 'alert-error', icon: 'mdi:alert-circle-outline' },
            warning: { className: 'alert-warning', icon: 'mdi:alert-outline' },
            success: { className: 'alert-success', icon: 'mdi:check-circle-outline' }
        };
        const style = types[type] || types.info;
        const oldAlerts = alertContainer.querySelectorAll('.alert-info, .alert-error, .alert-warning');
        oldAlerts.forEach(el => { if (!el.dataset.session) el.remove(); });
        const alert = document.createElement('div');
        alert.className = style.className;
        alert.style.animation = 'fadeIn 0.3s ease';
        alert.innerHTML = `
            <iconify-icon icon="${style.icon}" style="font-size:1.2rem;flex-shrink:0;"></iconify-icon>
            <span>${message}</span>
        `;
        alertContainer.prepend(alert);
    }

    const hasOpenedPopup = localStorage.getItem('midtrans_popup_opened_' + orderId) === 'true';

    if (hasOpenedPopup) {
        updateButton('pending');
        showAlert('Anda memiliki sesi pembayaran yang tertunda. Lanjutkan atau ganti metode.', 'warning');
    } else {
        showAlert('Pilih metode pembayaran di popup Midtrans.', 'info');
    }

    // 🔥 FUNGSI MEMBUKA POPUP SNAP
    function openSnapPopup() {
        window.snap.pay(currentSnapToken, {
            onSuccess: function(result) {
                localStorage.removeItem('midtrans_popup_opened_' + orderId);
                window.location.href = '{{ route("customer.midtrans.finish") }}?order_id=' + orderId;
            },
            onPending: function(result) {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Pembayaran pending. Lanjutkan atau ganti metode pembayaran lain.', 'warning');
            },
            onError: function(result) {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Pembayaran gagal. Silakan coba lagi atau ganti metode.', 'error');
            },
            onClose: function() {
                isProcessing = false;
                localStorage.setItem('midtrans_popup_opened_' + orderId, 'true');
                updateButton('pending');
                showAlert('Popup ditutup. Klik "Lanjutkan Pembayaran" atau "Ganti Metode" untuk memproses ulang.', 'warning');
            }
        });
    }

    // 🔥 TOMBOL 1: LANJUTKAN / BAYAR SEKARANG (Pakai token saat ini)
    btn.addEventListener('click', function() {
        if (isProcessing) return;
        isProcessing = true;
        updateButton('loading');
        openSnapPopup();
    });

    // 🔥 TOMBOL 2: GANTI METODE (Minta token baru ke server)
    btnChange.addEventListener('click', async function() {
        if (isProcessing) return;
        isProcessing = true;
        updateButton('loading');

        try {
            // Panggil API Refresh Token
            // PASTIKAN ROUTE INI SESUAI DENGAN YANG ADA DI web.php KAMU
            const url = `/customer/midtrans/${orderId}/refresh-token`; 
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (data.success) {
                currentSnapToken = data.snap_token;
                localStorage.removeItem('midtrans_popup_opened_' + orderId);
                showAlert('Silakan pilih metode pembayaran yang baru di popup.', 'info');
                openSnapPopup();
            } else {
                showAlert('Gagal memuat token baru: ' + (data.message || 'Error server'), 'error');
                updateButton('pending');
                isProcessing = false;
            }
        } catch (error) {
            showAlert('Terjadi kesalahan jaringan. Coba lagi.', 'error');
            updateButton('pending');
            isProcessing = false;
        }
    });

    // 🔥 CEK STATUS ORDER PERIODIK
    let checkCount = 0;
    const maxChecks = 30;

    function checkOrderStatus() {
        if (checkCount >= maxChecks) {
            clearInterval(checkInterval);
            return;
        }
        checkCount++;

        fetch('{{ route("customer.midtrans.check-status", $order) }}')
            .then(response => response.json())
            .then(data => {
                if (data.paid) {
                    clearInterval(checkInterval);
                    localStorage.removeItem('midtrans_popup_opened_' + orderId);
                    localStorage.removeItem('midtrans_last_method_' + orderId);
                    window.location.href = '{{ route("customer.checkout.success", $order) }}';
                }
            })
            .catch(() => {});
    }

    let checkInterval = setInterval(checkOrderStatus, 10000);

    // 🔥 CLEANUP
    window.addEventListener('beforeunload', function() {
        if (checkInterval) {
            clearInterval(checkInterval);
        }
    });

    console.log('🔍 Midtrans payment page loaded');
    console.log('📦 Order ID:', orderId);
});
</script>
@endsection