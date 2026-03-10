<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    public function run(): void
    {
        $menuItems = [
            ['name' => 'Kopi O', 'category' => 'Hot', 'price' => 2.00],
            ['name' => 'Kopi C', 'category' => 'Hot', 'price' => 2.50],
            ['name' => 'Kopi Susu', 'category' => 'Hot', 'price' => 3.00],
            ['name' => 'Teh Tarik', 'category' => 'Hot', 'price' => 2.50],
            ['name' => 'Teh O', 'category' => 'Hot', 'price' => 2.00],
            ['name' => 'Milo Panas', 'category' => 'Hot', 'price' => 3.00],
            ['name' => 'Kopi O Ais', 'category' => 'Cold', 'price' => 3.00],
            ['name' => 'Teh Tarik Ais', 'category' => 'Cold', 'price' => 3.50],
            ['name' => 'Milo Ais', 'category' => 'Cold', 'price' => 4.00],
            ['name' => 'Latte Ais', 'category' => 'Cold', 'price' => 5.00],
            ['name' => 'Americano Ais', 'category' => 'Cold', 'price' => 4.50],
            ['name' => 'Mocha Blended', 'category' => 'Blended', 'price' => 7.00],
            ['name' => 'Caramel Frappe', 'category' => 'Blended', 'price' => 8.00],
            ['name' => 'Matcha Latte', 'category' => 'Blended', 'price' => 7.50],
        ];

        $created = [];
        foreach ($menuItems as $item) {
            $created[] = MenuItem::create($item);
        }

        $customers = [
            ['name' => 'Ah Kau', 'phone' => '012-3456789'],
            ['name' => 'Siti Aminah', 'phone' => '013-9876543'],
            ['name' => 'Raju', 'phone' => '014-5551234'],
            ['name' => 'Tan Mei Ling', 'phone' => '016-7778899'],
            ['name' => 'Ahmad Faiz', 'phone' => '017-2223344'],
        ];

        $createdCust = [];
        foreach ($customers as $c) {
            $createdCust[] = Customer::create($c);
        }

        $statuses = ['completed', 'completed', 'completed', 'completed', 'pending', 'preparing'];
        $orderCountByDay = [];

        for ($i = 0; $i < 20; $i++) {
            $cust = $createdCust[array_rand($createdCust)];
            $daysAgo = rand(0, 6);
            $date = now()->subDays($daysAgo)->setHour(rand(7, 17))->setMinute(rand(0, 59));
            $dateKey = $date->format('Ymd');
            $orderCountByDay[$dateKey] = ($orderCountByDay[$dateKey] ?? 0) + 1;
            $orderNumber = 'ORD-' . $dateKey . '-' . str_pad($orderCountByDay[$dateKey], 4, '0', STR_PAD_LEFT);

            $numItems = rand(1, 4);
            $sel = array_rand($created, min($numItems, count($created)));
            if (!is_array($sel)) $sel = [$sel];

            $totalAmount = 0;
            $totalCups = 0;
            $itemsData = [];

            foreach ($sel as $idx) {
                $mi = $created[$idx];
                $qty = rand(1, 5);
                $sub = $mi->price * $qty;
                $totalAmount += $sub;
                $totalCups += $qty;
                $itemsData[] = ['menu_item_id' => $mi->id, 'quantity' => $qty, 'unit_price' => $mi->price, 'subtotal' => $sub];
            }

            $order = Order::create([
                'customer_id' => $cust->id,
                'order_number' => $orderNumber,
                'type' => count($sel) > 1 ? 'bulk' : 'single',
                'total_amount' => $totalAmount,
                'total_cups' => $totalCups,
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            foreach ($itemsData as $d) {
                $order->items()->create($d);
            }
        }
    }
}
