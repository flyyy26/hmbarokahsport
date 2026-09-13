<?php
// app/Http/Controllers/Admin/PasswordResetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{
    /**
     * List semua request reset password
     */
    public function index(Request $request)
    {
        $query = PasswordResetRequest::with(['user:id,name,phone,email', 'processedBy:id,name'])
            ->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->paginate(20)->withQueryString();

        // Hitung statistik
        $stats = [
            'pending' => PasswordResetRequest::where('status', 'pending')->count(),
            'approved' => PasswordResetRequest::where('status', 'approved')->count(),
            'rejected' => PasswordResetRequest::where('status', 'rejected')->count(),
            'used' => PasswordResetRequest::where('status', 'used')->count(),
        ];

        return view('admin.password-requests.index', compact('requests', 'stats'));
    }

    /**
     * Detail request
     */
    public function show(PasswordResetRequest $passwordRequest)
    {
        $passwordRequest->load(['user', 'processedBy']);

        return view('admin.password-requests.show', [
            'resetRequest' => $passwordRequest,
        ]);
    }

    /**
     * Approve request → generate token
     */
    public function approve(Request $request, PasswordResetRequest $passwordRequest)
    {
        if ($passwordRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $passwordRequest->approve(
            Auth::id(),
            $request->integer('expiry_hours', 24)
        );

        $resetLink = route('customer.reset-password', $passwordRequest->token);

        return back()->with([
            'success' => 'Permintaan berhasil disetujui. Link reset sudah dibuat.',
            'reset_link' => $resetLink,
        ]);
    }

    /**
     * Reject request
     */
    public function reject(Request $request, PasswordResetRequest $passwordRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($passwordRequest->status !== 'pending') {
            return back()->with('error', 'Request ini sudah diproses sebelumnya.');
        }

        $passwordRequest->reject(
            Auth::id(),
            $request->admin_note
        );

        return back()->with('success', 'Permintaan berhasil ditolak.');
    }

    /**
     * Hapus request (cleanup)
     */
    public function destroy(PasswordResetRequest $passwordRequest)
    {
        $passwordRequest->delete();

        return back()->with('success', 'Request berhasil dihapus.');
    }
}