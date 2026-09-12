<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

class ReturnFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_button_appears_on_completed_order()
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'phone' => '081234567890',
            'password' => bcrypt('password'),
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'shipping_status' => 'delivered',
            'delivered_at' => now(),
            'return_status' => null,
            'payment_status' => 'paid',
        ]);

        $this->actingAs($user, 'customer');

        $response = $this->get(route('customer.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('Retur / Pengembalian');
        $response->assertSee('showReturnModal');
    }

    public function test_return_button_not_appears_on_shipped_order()
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'phone' => '081234567890',
            'password' => bcrypt('password'),
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'shipping_status' => 'shipped',
            'delivered_at' => null,
            'return_status' => null,
        ]);

        $this->actingAs($user, 'customer');

        $response = $this->get(route('customer.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('Pesanan Diterima');
        $response->assertDontSee('showReturnModal');
    }

    public function test_confirm_receipt_then_return_works()
    {
        $user = User::factory()->create([
            'role' => 'customer',
            'phone' => '081234567890',
            'password' => bcrypt('password'),
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'shipping_status' => 'shipped',
            'delivered_at' => null,
            'return_status' => null,
        ]);

        $this->actingAs($user, 'customer');

        // Confirm receipt
        $response = $this->post(route('customer.orders.confirm-received', $order));
        $response->assertRedirect();

        // Verify status changed
        $order->refresh();
        $this->assertEquals('delivered', $order->shipping_status);
        $this->assertNotNull($order->delivered_at);

        // Now order detail should show return button
        $response = $this->get(route('customer.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('Retur / Pengembalian');

        // Submit return request
        $response = $this->post(route('customer.orders.request-return', $order), [
            'reason' => 'Produk rusak pada pengiriman',
        ]);
        $response->assertRedirect();

        // Verify return status updated
        $order->refresh();
        $this->assertEquals('pending', $order->return_status);

        // Admin should see the return
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $adminResponse = $this->get(route('admin.returns.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee($order->order_number);
    }
}
