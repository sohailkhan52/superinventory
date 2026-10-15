<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request): void {
                // Generate a unique shop code
                $code = 'SHOP-' . strtoupper(Str::random(6));

                // Ensure uniqueness (very unlikely to collide, but safe)
                while (Shop::where('code', $code)->exists()) {
                    $code = 'SHOP-' . strtoupper(Str::random(6));
                }

                $shop = Shop::create([
                    'name' => $request->shop_name,
                    'code' => $code,
                    'email' => $request->email,
                    'owner_name' => $request->name,
                    'city' => $request->shop_city,
                    'phone' => $request->shop_phone,
                    'address' => $request->shop_address,
                    'status' => 'pending',
                ]);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'status' => 'pending',
                    'shop_id' => $shop->id,
                ]);

                // Assign Admin role
                $user->assignRole('Admin');
            });

            return redirect()->route('login')
                ->with('success', 'Shop and account created successfully! Your shop is pending approval from the super admin.');
        } catch (\Throwable $e) {
            Log::error('Shop registration failed.', ['exception' => $e]);

            return back()->withInput()->with('error', 'We could not create your shop and account. Please try again or contact support.');
        }
    }
}
