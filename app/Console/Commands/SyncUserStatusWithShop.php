<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Console\Command;

class SyncUserStatusWithShop extends Command
{
    protected $signature = 'sync:user-shop-status {--shop-id=}';
    protected $description = 'Sync user status with their shop status';

    public function handle()
    {
        $shopId = $this->option('shop-id');
        
        if ($shopId) {
            $shops = Shop::where('id', $shopId)->get();
        } else {
            $shops = Shop::all();
        }
        
        $this->info('Syncing user statuses with their shops...');
        
        foreach ($shops as $shop) {
            $users = User::where('shop_id', $shop->id)->get();
            
            foreach ($users as $user) {
                if ($user->status !== $shop->status) {
                    $oldStatus = $user->status;
                    $user->status = $shop->status;
                    $user->save();
                    
                    // Ensure Admin role is assigned
                    if ($shop->status === 'approved' && !$user->hasRole('Admin')) {
                        $user->assignRole('Admin');
                        $this->info("✓ {$user->email}: {$oldStatus} → {$shop->status} + Admin role assigned");
                    } else {
                        $this->info("✓ {$user->email}: {$oldStatus} → {$shop->status}");
                    }
                }
            }
        }
        
        $this->info('Done!');
        return 0;
    }
}
