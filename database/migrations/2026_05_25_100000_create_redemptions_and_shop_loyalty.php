<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add loyalty/redemption settings to shops
        Schema::table('shops', function (Blueprint $table) {
            $table->unsignedInteger('redemption_threshold')->nullable()->after('day_start_time')
                ->comment('Points required to redeem a reward. NULL = disabled.');
            $table->string('redemption_reward', 255)->nullable()->after('redemption_threshold')
                ->comment('Description of the reward, e.g. "1 Free Drink".');
        });

        // Redemptions table
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points_used');
            $table->string('redemption_code', 8)->unique();
            $table->enum('status', ['pending', 'used', 'expired'])->default('pending');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['shop_id', 'status']);
            $table->index(['customer_id', 'shop_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemptions');
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['redemption_threshold', 'redemption_reward']);
        });
    }
};
