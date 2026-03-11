<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAddon;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $shop = auth()->user()->shop;
        return view('shop.settings.index', compact('shop'));
    }

    public function apiIndex()
    {
        $shop = auth()->user()->shop;
        return response()->json(['shop' => $shop]);
    }

    public function updateShop(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'initial' => 'nullable|string|max:10|alpha_dash',
            'shop_address' => 'nullable|string|max:500',
            'shop_logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('shop_name', 'initial', 'shop_address');

        if ($request->hasFile('shop_logo')) {
            if ($shop->shop_logo) {
                Storage::disk('public')->delete($shop->shop_logo);
            }
            $data['shop_logo'] = $request->file('shop_logo')->store('shop-logos', 'public');
        }

        $shop->update($data);
        return back()->with('success', 'Shop details updated!');
    }

    public function apiUpdateShop(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'shop_name' => 'required|string|max:255',
            'initial' => 'nullable|string|max:10|alpha_dash',
            'shop_address' => 'nullable|string|max:500',
            'shop_logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('shop_name', 'initial', 'shop_address');

        if ($request->hasFile('shop_logo')) {
            if ($shop->shop_logo) {
                Storage::disk('public')->delete($shop->shop_logo);
            }
            $data['shop_logo'] = $request->file('shop_logo')->store('shop-logos', 'public');
        }

        $shop->update($data);
        return response()->json(['message' => 'Shop details updated!', 'shop' => $shop]);
    }

    public function updateColor(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        $shop->update([
            'color_setting' => [
                'primary' => $request->primary_color,
                'secondary' => $request->secondary_color,
            ]
        ]);

        return back()->with('success', 'Color settings updated!');
    }

    public function apiUpdateColor(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        $shop->update([
            'color_setting' => [
                'primary' => $request->primary_color,
                'secondary' => $request->secondary_color,
            ]
        ]);

        return response()->json(['message' => 'Color settings updated!', 'shop' => $shop]);
    }

    public function products()
    {
        $shop = auth()->user()->shop;
        $products = Product::with(['variants', 'addons'])->where('shop_id', $shop->id)->orderBy('name')->get();
        return view('shop.settings.products', compact('products', 'shop'));
    }

    public function apiProducts()
    {
        $shop = auth()->user()->shop;
        $products = Product::with(['variants', 'addons'])->where('shop_id', $shop->id)->orderBy('name')->get();
        return response()->json(['products' => $products, 'shop' => $shop]);
    }

    public function storeProduct(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:50',
            'variants.*.price_modifier' => 'nullable|numeric',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required|string|max:100',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'name' => $request->name,
            'price' => $request->price,
        ]);

        if ($request->filled('variants')) {
            foreach ($request->variants as $v) {
                $product->variants()->create([
                    'name' => $v['name'],
                    'price_modifier' => $v['price_modifier'] ?? 0,
                ]);
            }
        }

        if ($request->filled('addons')) {
            foreach ($request->addons as $a) {
                $product->addons()->create([
                    'name' => $a['name'],
                    'price' => $a['price'] ?? 0,
                ]);
            }
        }

        return back()->with('success', "Product '{$product->name}' added!");
    }

    public function apiStoreProduct(Request $request)
    {
        $shop = auth()->user()->shop;

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string|max:50',
            'variants.*.price_modifier' => 'nullable|numeric',
            'variants.*.is_default' => 'nullable|boolean',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required|string|max:100',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'name' => $request->name,
            'price' => $request->price,
        ]);

        if ($request->filled('variants')) {
            foreach ($request->variants as $v) {
                $product->variants()->create([
                    'name' => $v['name'],
                    'price_modifier' => $v['price_modifier'] ?? 0,
                    'is_default' => $v['is_default'] ?? false,
                ]);
            }
        }

        if ($request->filled('addons')) {
            foreach ($request->addons as $a) {
                $product->addons()->create([
                    'name' => $a['name'],
                    'price' => $a['price'] ?? 0,
                ]);
            }
        }

        $product->load(['variants', 'addons']);
        return response()->json(['message' => "Product '{$product->name}' added!", 'product' => $product], 201);
    }

    public function apiUpdateProduct(Request $request, Product $product)
    {
        $shop = auth()->user()->shop;

        if ($product->shop_id !== $shop->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:50',
            'variants.*.price_modifier' => 'nullable|numeric',
            'variants.*.is_default' => 'nullable|boolean',
            'addons' => 'nullable|array',
            'addons.*.id' => 'nullable|integer|exists:product_addons,id',
            'addons.*.name' => 'required|string|max:100',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        // Sync Variants
        if ($request->has('variants')) {
            $providedVariantIds = collect($request->variants)->pluck('id')->filter()->toArray();
            $product->variants()->whereNotIn('id', $providedVariantIds)->delete();

            foreach ($request->variants as $v) {
                $variantData = [
                    'name' => $v['name'],
                    'price_modifier' => $v['price_modifier'] ?? 0,
                    'is_default' => $v['is_default'] ?? false,
                ];

                if (isset($v['id'])) {
                    $product->variants()->where('id', $v['id'])->update($variantData);
                } else {
                    $product->variants()->create($variantData);
                }
            }
        } else {
            $product->variants()->delete();
        }

        // Sync Addons
        if ($request->has('addons')) {
            $providedAddonIds = collect($request->addons)->pluck('id')->filter()->toArray();
            $product->addons()->whereNotIn('id', $providedAddonIds)->delete();

            foreach ($request->addons as $a) {
                $addonData = [
                    'name' => $a['name'],
                    'price' => $a['price'] ?? 0,
                ];

                if (isset($a['id'])) {
                    $product->addons()->where('id', $a['id'])->update($addonData);
                } else {
                    $product->addons()->create($addonData);
                }
            }
        } else {
            $product->addons()->delete();
        }

        $product->load(['variants', 'addons']);
        return response()->json(['message' => "Product '{$product->name}' updated!", 'product' => $product], 200);
    }

    public function toggleProduct(Product $product)
    {
        $shop = auth()->user()->shop;
        abort_if($product->shop_id !== $shop->id, 403);
        $product->update(['is_available' => !$product->is_available]);
        return back()->with('success', "{$product->name} " . ($product->is_available ? 'enabled' : 'disabled') . '.');
    }

    public function apiToggleProduct(Product $product)
    {
        $shop = auth()->user()->shop;
        abort_if($product->shop_id !== $shop->id, 403);
        $product->update(['is_available' => !$product->is_available]);
        return response()->json(['message' => "{$product->name} " . ($product->is_available ? 'enabled' : 'disabled') . '.', 'product' => $product]);
    }

    public function destroyProduct(Product $product)
    {
        $shop = auth()->user()->shop;
        abort_if($product->shop_id !== $shop->id, 403);
        $product->delete();
        return back()->with('success', "{$product->name} deleted.");
    }

    public function apiDestroyProduct(Product $product)
    {
        $shop = auth()->user()->shop;
        abort_if($product->shop_id !== $shop->id, 403);
        $product->delete();
        return response()->json(['message' => "{$product->name} deleted."]);
    }

    public function userSettings()
    {
        return view('shop.settings.user');
    }

    public function apiUserSettings()
    {
        return response()->json(['user' => auth()->user()]);
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($request->only('name', 'email'));
        return back()->with('success', 'Profile updated!');
    }

    public function apiUpdateUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($request->only('name', 'email'));
        return response()->json(['message' => 'Profile updated!', 'user' => auth()->user()]);
    }
}
