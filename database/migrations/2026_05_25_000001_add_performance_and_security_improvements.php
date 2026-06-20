<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add tracking_token to orders
        if (!Schema::hasColumn('orders', 'tracking_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('tracking_token', 64)->nullable()->after('status');
                $table->index('tracking_token');
            });
        }

        // Add missing indexes on orders
        $orderIndexes = Schema::getIndexListing('orders');
        Schema::table('orders', function (Blueprint $table) use ($orderIndexes) {
            if (!in_array('orders_shop_id_status_index', $orderIndexes)) {
                $table->index(['shop_id', 'status']);
            }
            if (!in_array('orders_shop_id_is_archived_index', $orderIndexes)) {
                $table->index(['shop_id', 'is_archived']);
            }
            if (!in_array('orders_shop_id_business_date_index', $orderIndexes)) {
                $table->index(['shop_id', 'business_date']);
            }
        });

        // Add missing indexes on customers
        $customerIndexes = Schema::getIndexListing('customers');
        Schema::table('customers', function (Blueprint $table) use ($customerIndexes) {
            if (!in_array('customers_shop_id_index', $customerIndexes)) {
                $table->index('shop_id');
            }
            if (!in_array('customers_user_id_index', $customerIndexes)) {
                $table->index('user_id');
            }
        });

        // Add unique constraint on shops.initial
        $shopIndexes = Schema::getIndexListing('shops');
        Schema::table('shops', function (Blueprint $table) use ($shopIndexes) {
            if (!in_array('shops_initial_unique', $shopIndexes)) {
                $table->unique('initial');
            }
        });
    }

    public function down(): void
    {
        $orderIndexes = Schema::getIndexListing('orders');
        Schema::table('orders', function (Blueprint $table) use ($orderIndexes) {
            if (in_array('orders_shop_id_status_index', $orderIndexes)) {
                $table->dropIndex(['shop_id', 'status']);
            }
            if (in_array('orders_shop_id_is_archived_index', $orderIndexes)) {
                $table->dropIndex(['shop_id', 'is_archived']);
            }
            if (in_array('orders_shop_id_business_date_index', $orderIndexes)) {
                $table->dropIndex(['shop_id', 'business_date']);
            }
        });

        if (Schema::hasColumn('orders', 'tracking_token')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('tracking_token');
            });
        }

        $customerIndexes = Schema::getIndexListing('customers');
        Schema::table('customers', function (Blueprint $table) use ($customerIndexes) {
            if (in_array('customers_shop_id_index', $customerIndexes)) {
                $table->dropIndex(['shop_id']);
            }
            if (in_array('customers_user_id_index', $customerIndexes)) {
                $table->dropIndex(['user_id']);
            }
        });

        $shopIndexes = Schema::getIndexListing('shops');
        Schema::table('shops', function (Blueprint $table) use ($shopIndexes) {
            if (in_array('shops_initial_unique', $shopIndexes)) {
                $table->dropUnique(['initial']);
            }
        });
    }
};
