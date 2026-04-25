<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function apiShow()
    {
        return response()->json(['user' => auth()->user()]);
    }

    public function apiUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update($request->only('name', 'email'));
        return response()->json(['message' => 'Profile updated!', 'user' => auth()->user()]);
    }

    public function apiUpdatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'message' => 'The provided password does not match our records.',
                'errors' => ['current_password' => ['The provided password does not match our records.']]
            ], 422);
        }

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully!']);
    }
}
