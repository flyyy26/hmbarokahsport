<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where(function($q) {
            $q->where('role', 'customer')
            ->orWhereNull('role');
        });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $users = $query->withCount('orders')           // ✅ 'orders' bukan 'order'
            ->withSum('orders', 'total')                // ✅ 'orders' bukan 'order'
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->except('page'));

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['addresses', 'orders' => function($q) {
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
}