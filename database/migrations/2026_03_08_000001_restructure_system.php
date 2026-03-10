<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add role to users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['shop', 'customer', 'system_owner'])->default('shop')->after('email');
        });

        // 2. Create shops table
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('shop_name');
            $table->string('shop_logo')->nullable();
            $table->text('shop_address')->nullable();
            $table->json('color_setting')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Drop old menu_items and create products
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('menu_items');

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 4. Create product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name'); // e.g. cold, hot
            $table->decimal('price_modifier', 8, 2)->default(0);
            $table->timestamps();
        });

        // 5. Create product_addons
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name'); // e.g. extra shot, milk type
            $table->decimal('price', 8, 2)->default(0);
            $table->timestamps();
        });

        // 6. Update customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->after('user_id')->constrained('shops')->cascadeOnDelete();
            $table->integer('collect_points')->default(0)->after('notes');
        });

        // 7. Update orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->cascadeOnDelete();
        });

        // 8. Recreate order_items with product reference
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->string('variant')->nullable();
            $table->json('addons')->nullable();
            $table->timestamps();
        });

        // 9. Create favorite_orders
        Schema::create('favorite_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name');
            $table->json('order_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('shops');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shop_id']);
            $table->dropColumn('shop_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['shop_id']);
            $table->dropColumn(['user_id', 'shop_id', 'collect_points']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Recreate original menu_items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('price', 8, 2);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Recreate original order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }
};
