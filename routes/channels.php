<?php

use Illuminate\Support\Facades\Broadcast;

/*
 * Authorise the shop owner to listen on their own private channel.
 * The authenticated user must own the shop whose ID is in the channel name.
 */
Broadcast::channel('shop.{shopId}', function ($user, $shopId) {
    return $user->shop && (int) $user->shop->id === (int) $shopId;
});
