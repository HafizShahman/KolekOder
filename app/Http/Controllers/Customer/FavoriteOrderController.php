<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FavoriteOrder;
use Illuminate\Http\Request;

class FavoriteOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        $favorites = FavoriteOrder::whereIn('customer_id', $customerIds)->latest()->get();
        return view('customer.favorites.index', compact('favorites'));
    }

    public function destroy(FavoriteOrder $favoriteOrder)
    {
        $user = auth()->user();
        $customerIds = Customer::where('user_id', $user->id)->pluck('id');
        abort_if(!$customerIds->contains($favoriteOrder->customer_id), 403);
        $favoriteOrder->delete();
        return back()->with('success', 'Favorite removed.');
    }
}
