<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create System Owner
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@kolekoder.com',
            'password' => Hash::make('password'),
            'role' => 'system_owner',
        ]);

        // Create a sample shop owner
        $shopUser = User::create([
            'name' => 'Shop Owner',
            'email' => 'shop@kolekoder.com',
            'password' => Hash::make('password'),
            'role' => 'shop',
        ]);

        Shop::create([
            'user_id' => $shopUser->id,
            'shop_name' => 'Demo Coffee',
            'shop_address' => 'Jalan Demo, 12345',
            'is_active' => true,
        ]);

        // Create a sample customer
        User::create([
            'name' => 'Test Customer',
            'email' => 'customer@kolekoder.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);
    }
}
