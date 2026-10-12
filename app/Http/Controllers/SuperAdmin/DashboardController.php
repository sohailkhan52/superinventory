<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard with shop statistics.
     */
    public function index(): View
    {
        $totalShops     = 0;
        $pendingShops   = 0;
        $approvedShops  = 0;
        $rejectedShops  = 0;
        $suspendedShops = 0;
        $totalUsers     = 0;
        $activeUsers    = 0;
        $inactiveUsers  = 0;
        $suspendedUsers = 0;
        $recentPendingShops = collect();
        $recentShops        = collect();
        $shopsByStatus = [
            'pending'   => 0,
            'approved'  => 0,
            'rejected'  => 0,
            'suspended' => 0,
        ];

        try {
            // Shop statistics
            $totalShops     = Shop::count();
            $pendingShops   = Shop::where('status', 'pending')->count();
            $approvedShops  = Shop::where('status', 'approved')->count();
            $rejectedShops  = Shop::where('status', 'rejected')->count();
            $suspendedShops = Shop::where('status', 'suspended')->count();

            // User statistics (withoutGlobalScopes so super admin sees all)
            $totalUsers     = User::withoutGlobalScopes()->count();
            $activeUsers    = User::withoutGlobalScopes()->where('status', 'active')->count();
            $inactiveUsers  = User::withoutGlobalScopes()->where('status', 'inactive')->count();
            $suspendedUsers = User::withoutGlobalScopes()->where('status', 'suspended')->count();

            // Recent pending shops awaiting approval
            $recentPendingShops = Shop::where('status', 'pending')
                ->with('owner')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            // Recent shops (all statuses)
            $recentShops = Shop::with('owner')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $shopsByStatus = [
                'pending'   => $pendingShops,
                'approved'  => $approvedShops,
                'rejected'  => $rejectedShops,
                'suspended' => $suspendedShops,
            ];
        } catch (\Exception $e) {
            \Log::error('SuperAdmin Dashboard error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }

        return view('super-admin.dashboard', compact(
            'totalShops',
            'pendingShops',
            'approvedShops',
            'rejectedShops',
            'suspendedShops',
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'suspendedUsers',
            'recentPendingShops',
            'recentShops',
            'shopsByStatus'
        ));
    }
}
