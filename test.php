<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$items = App\Models\OrderItem::whereNotNull('variant')->get();
foreach($items as $order) {
    echo "ID: " . $order->id . "\n";
    echo "Variant: " . json_encode($order->variant) . "\n";
}
