<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * List customers + tab permintaan reset password
     */
    public function index(Request $request)
    {
        // ============================================
        // TAB 1: CUSTOMERS
        // ============================================
        $query = User::where(function ($q) {
            $q->where('role', 'customer')->orWhereNull('role');
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $users = $query->withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'users_page')
            ->appends($request->except(['users_page', 'requests_page']));

        // ============================================
        // TAB 2: PASSWORD RESET REQUESTS
        // ============================================
        $requestQuery = PasswordResetRequest::with([
            'user:id,name,email,phone,is_active',
            'processedBy:id,name',
        ])->latest();

        if ($request->filled('reset_search')) {
            $s = $request->reset_search;
            $requestQuery->where(function ($q) use ($s) {
                $q->where('phone', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('reset_status')) {
            $requestQuery->where('status', $request->reset_status);
        }

        $resetRequests = $requestQuery->paginate(15, ['*'], 'requests_page')
            ->appends($request->except(['users_page', 'requests_page']));

        // Statistik pending
        $pendingResetCount = PasswordResetRequest::where('status', 'pending')->count();

        return view('admin.users.index', compact(
            'users',
            'resetRequests',
            'pendingResetCount'
        ));
    }

    public function show($id)
    {
        $user = User::with(['addresses', 'orders' => function ($q) {
            $q->latest()->limit(5);
        }])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => $request->boolean('is_active')]);

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus.',
        ]);
    }

    // ============================================
    // 🔐 PASSWORD RESET APPROVAL
    // ============================================

    /**
     * Approve password reset request → generate token
     */
    public function approveReset(Request $request, PasswordResetRequest $passwordRequest)
    {
        if ($passwordRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $expiryHours = $request->integer('expiry_hours', 24);

        $passwordRequest->approve(Auth::id(), $expiryHours);

        $resetLink = route('customer.reset-password', $passwordRequest->token);

        return back()->with([
            'success' => 'Permintaan reset password disetujui untuk ' . $passwordRequest->user->name,
            'reset_link' => $resetLink,
            'reset_user_name' => $passwordRequest->user->name,
            'reset_phone' => $passwordRequest->phone,
        ]);
    }

    /**
     * Reject password reset request
     */
    public function rejectReset(Request $request, PasswordResetRequest $passwordRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($passwordRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        // Reject request
        $passwordRequest->reject(Auth::id(), $request->admin_note);

        // 🔥 Build WhatsApp link ke customer
        $customerPhone = preg_replace('/[^0-9]/', '', $passwordRequest->phone);
        if (str_starts_with($customerPhone, '0')) {
            $customerPhone = '62' . substr($customerPhone, 1);
        } elseif (str_starts_with($customerPhone, '8')) {
            $customerPhone = '62' . $customerPhone;
        }

        // 🔥 Ambil nama toko dari settings
        $shopName = \App\Models\Setting::first()->shop_name ?? 'Barokah Sport';

        // 🔥 Template pesan penolakan
        $reason = $request->admin_note ?: 'Identitas tidak dapat diverifikasi.';
        
        $waMessage = "Halo *{$passwordRequest->user->name}*,\n\n"
            . "Mohon maaf, permintaan *reset password* Anda di *{$shopName}* tidak dapat kami setujui.\n\n"
            . "📝 *Alasan:*\n{$reason}\n\n"
            . "Silakan:\n"
            . "• Pastikan data yang Anda masukkan sesuai\n"
            . "• Hubungi admin langsung jika ada kendala\n\n"
            . "Terima kasih. 🙏";

        $waLink = 'https://wa.me/' . $customerPhone . '?text=' . urlencode($waMessage);

        return back()
            ->with('success', 'Permintaan reset password untuk ' . $passwordRequest->user->name . ' telah ditolak.')
            ->with('reject_wa_link', $waLink)
            ->with('reject_customer_name', $passwordRequest->user->name)
            ->with('reject_customer_phone', $passwordRequest->phone);
    }
}