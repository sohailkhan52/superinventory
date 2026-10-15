<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * List all shops.
     */
    public function index(): View
    {
        $shops = Shop::with('owner')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('super-admin.shops.index', compact('shops'));
    }

    /**
     * Show a single shop.
     */
    public function show(Shop $shop): View
    {
        $shop->load('owner');
        return view('super-admin.shops.show', compact('shop'));
    }

    /**
     * Approve a pending shop.
     */
    public function approve(Shop $shop): RedirectResponse
    {
        $shop->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Approve all users in the shop and assign Admin role if missing
        $shop->users()->update(['status' => 'approved']);
        
        foreach ($shop->users()->get() as $user) {
            if (!$user->hasRole('Admin')) {
                $user->assignRole('Admin');
            }
        }

        return back()->with('success', "Shop \"{$shop->name}\" has been approved.");
    }

    /**
     * Reject a shop.
     */
    public function reject(Shop $shop): RedirectResponse
    {
        $shop->update(['status' => 'rejected']);

        // Reject all users in the shop
        $shop->users()->update(['status' => 'rejected']);

        return back()->with('success', "Shop \"{$shop->name}\" has been rejected.");
    }

    /**
     * Suspend an approved shop.
     */
    public function suspend(Shop $shop): RedirectResponse
    {
        $shop->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        // Suspend all users in the shop
        $shop->users()->update(['status' => 'suspended']);

        return back()->with('success', "Shop \"{$shop->name}\" has been suspended.");
    }

    /**
     * Reactivate a suspended shop.
     */
    public function activate(Shop $shop): RedirectResponse
    {
        $shop->update([
            'status' => 'approved',
            'suspended_at' => null,
        ]);

        // Reactivate all users in the shop
        $shop->users()->update(['status' => 'approved']);
        
        foreach ($shop->users()->get() as $user) {
            if (!$user->hasRole('Admin')) {
                $user->assignRole('Admin');
            }
        }

        return back()->with('success', 'Shop reactivated.');
    }
}
