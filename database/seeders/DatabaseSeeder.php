<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed default admin user
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'phone' => '998901234567',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        // Seed a regular user
        User::query()->updateOrCreate(
            ['email' => 'user1@example.com'],
            [
                'name' => 'Oddiy Foydalanuvchi',
                'username' => 'user1',
                'phone' => '998909999999',
                'role' => 'user',
                'password' => bcrypt('password'),
            ]
        );

        // Seed default settings
        \App\Models\AdminSetting::query()->firstOrCreate([], [
            'language' => 'uz',
            'service_fee_percent' => 5.00,
        ]);

        // Create a sample order with one item and one payment
        $user = User::where('username', 'admin')->first();
        if ($user) {
            $order = \App\Models\Order::query()->create([
                'user_id' => $user->id,
                'source_platform' => '1688',
                'product_url' => 'https://example.com/product-demo',
                'status' => 'pending',
                'tracking_number' => null,
                'total_price' => 0,
            ]);

            $item = \App\Models\OrderItem::query()->create([
                'order_id' => $order->id,
                'title' => 'Demo Mahsulot',
                'image_url' => null,
                'sku' => 'SKU-001',
                'size' => 'L',
                'color' => 'Blue',
                'quantity' => 2,
                'unit_price' => 150000.00,
                'subtotal' => 300000.00,
            ]);

            $order->update(['total_price' => $item->subtotal]);

            \App\Models\Payment::query()->create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'currency' => 'UZS',
                'status' => 'pending',
                'receipt_path' => null,
                'note' => 'Demo to\'lov',
            ]);
        }
    }
}
