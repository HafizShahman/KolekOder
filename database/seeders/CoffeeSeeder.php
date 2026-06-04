<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class CoffeeSeeder extends Seeder
{
    public function run(): void
    {
        $shop = Shop::first();

        if (!$shop) {
            $this->command->warn('No shop found. Skipping CoffeeSeeder. Create a shop first.');
            return;
        }

        $menuItems = [
            ['name' => 'Kopi O',          'price' => 2.00],
            ['name' => 'Kopi C',          'price' => 2.50],
            ['name' => 'Kopi Susu',       'price' => 3.00],
            ['name' => 'Teh Tarik',       'price' => 2.50],
            ['name' => 'Teh O',           'price' => 2.00],
            ['name' => 'Milo Panas',      'price' => 3.00],
            ['name' => 'Kopi O Ais',      'price' => 3.00],
            ['name' => 'Teh Tarik Ais',   'price' => 3.50],
            ['name' => 'Milo Ais',        'price' => 4.00],
            ['name' => 'Latte Ais',       'price' => 5.00],
            ['name' => 'Americano Ais',   'price' => 4.50],
            ['name' => 'Mocha Blended',   'price' => 7.00],
            ['name' => 'Caramel Frappe',  'price' => 8.00],
            ['name' => 'Matcha Latte',    'price' => 7.50],
        ];

        foreach ($menuItems as $index => $item) {
            Product::firstOrCreate(
                ['shop_id' => $shop->id, 'name' => $item['name']],
                ['price' => $item['price'], 'is_available' => true, 'sort_order' => $index]
            );
        }

        $this->command->info('CoffeeSeeder: ' . count($menuItems) . ' products seeded for shop "' . $shop->shop_name . '".');
    }
}
