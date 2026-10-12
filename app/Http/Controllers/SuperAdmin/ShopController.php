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
        $shop->update(['status' => 'approved']);

        // Also activate the shop owner account
        if ($shop->owner) {
            $shop->owner->update(['status' => 'active']);
        }

        return back()->with('success', "Shop \"{$shop->name}\" has been approved.");
    }

    /**
     * Reject a shop.
     */
    public function reject(Shop $shop): RedirectResponse
    {
        $shop->update(['status' => 'rejected']);

        // Deactivate the shop owner account
        if ($shop->owner) {
            $shop->owner->update(['status' => 'inactive']);
        }

        return back()->with('success', "Shop \"{$shop->name}\" has been rejected.");
    }

    /**
     * Suspend an approved shop.
     */
    public function suspend(Shop $shop): RedirectResponse
    {
        $shop->update(['status' => 'suspended']);

        // Suspend the shop owner account
        if ($shop->owner) {
            $shop->owner->update(['status' => 'suspended']);
        }

        return back()->with('success', "Shop \"{$shop->name}\" has been suspended.");
    }
}
