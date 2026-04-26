<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->longText('shop_logo')->nullable()->change();
        });

        // Convert existing images to base64
        $shops = \App\Models\Shop::whereNotNull('shop_logo')->get();
        foreach ($shops as $shop) {
            // Only convert if it's not already a base64 string
            if (!str_starts_with($shop->shop_logo, 'data:image')) {
                $path = storage_path('app/public/' . $shop->shop_logo);
                if (file_exists($path)) {
                    $mime = mime_content_type($path);
                    $data = file_get_contents($path);
                    $base64 = 'data:' . $mime . ';base64,' . base64_encode($data);
                    
                    \Illuminate\Support\Facades\DB::table('shops')
                        ->where('id', $shop->id)
                        ->update(['shop_logo' => $base64]);
                }
            }
        }

        // Optional: Clean up directory
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists('shop-logos')) {
            \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('shop-logos');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->string('shop_logo', 255)->nullable()->change();
        });
    }
};
