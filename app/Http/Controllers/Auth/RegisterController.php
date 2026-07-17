<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
            ]);

            return $user;
        });
    }

    public function apiRegister(\Illuminate\Http\Request $request)
    {
        $accountType = $request->input('account_type', 'customer');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_type' => ['nullable', 'in:customer,shop'],
        ];

        if ($accountType === 'shop') {
            $rules['shop_name'] = ['required', 'string', 'max:255'];
            $rules['initial'] = ['required', 'string', 'max:10', 'alpha_dash', 'unique:shops,initial'];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = DB::transaction(function () use ($request, $accountType) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $accountType === 'shop' ? 'shop' : 'customer',
            ]);

            if ($accountType === 'shop') {
                Shop::create([
                    'user_id' => $user->id,
                    'shop_name' => $request->shop_name,
                    'initial' => $request->initial,
                    'is_active' => true,
                ]);
            }

            return $user;
        });

        // Authenticate the user directly
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token
        ], 201);
    }
}
