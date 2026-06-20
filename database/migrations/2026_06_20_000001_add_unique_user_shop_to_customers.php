<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Merge any existing duplicate (user_id, shop_id) customer rows before
        // enforcing uniqueness, so the index can be created without errors and
        // so loyalty points aren't left split across duplicate records.
        $duplicateGroups = DB::table('customers')
            ->select('user_id', 'shop_id', DB::raw('MIN(id) as keep_id'), DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('user_id')
            ->whereNotNull('shop_id')
            ->groupBy('user_id', 'shop_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicateGroups as $group) {
            $dupeIds = DB::table('customers')
                ->where('user_id', $group->user_id)
                ->where('shop_id', $group->shop_id)
                ->where('id', '!=', $group->keep_id)
                ->pluck('id');

            if ($dupeIds->isEmpty()) {
                continue;
            }

            // Roll the duplicates' points into the surviving record
            $extraPoints = (int) DB::table('customers')->whereIn('id', $dupeIds)->sum('collect_points');
            if ($extraPoints > 0) {
                DB::table('customers')->where('id', $group->keep_id)->increment('collect_points', $extraPoints);
            }

            // Re-point related rows to the surviving customer
            DB::table('orders')->whereIn('customer_id', $dupeIds)->update(['customer_id' => $group->keep_id]);
            DB::table('favorite_orders')->whereIn('customer_id', $dupeIds)->update(['customer_id' => $group->keep_id]);

            // Remove the duplicates
            DB::table('customers')->whereIn('id', $dupeIds)->delete();
        }

        Schema::table('customers', function (Blueprint $table) {
            // Guest customers (user_id NULL) are exempt — NULLs are not deduplicated by a unique index.
            $table->unique(['user_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'shop_id']);
        });
    }
};
