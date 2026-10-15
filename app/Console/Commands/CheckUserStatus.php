<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Console\Command;

class CheckUserStatus extends Command
{
    protected $signature = 'check:user-status';
    protected $description = 'Check users with pending status and their shops';

    public function handle()
    {
        $this->info('Checking user statuses...');
        
        $pendingUsers = User::where('status', 'pending')->get();
        $this->info('Pending users: ' . $pendingUsers->count());
        
        foreach ($pendingUsers as $user) {
            $this->line('');
            $this->line("Email: {$user->email}");
            $this->line("Name: {$user->name}");
            $this->line("Shop ID: {$user->shop_id}");
            
            if ($user->shop_id) {
                $shop = Shop::find($user->shop_id);
                if ($shop) {
                    $this->line("Shop: {$shop->name}");
                    $this->line("Shop Status: {$shop->status}");
                    
                    if ($shop->status === 'approved') {
                        $this->warn("⚠️  Shop is approved but user is still pending!");
                    }
                }
            }
        }
        
        $approvedUsers = User::where('status', 'approved')->count();
        $this->info("\nApproved users: {$approvedUsers}");
        
        $activeUsers = User::where('status', 'active')->count();
        $this->info("Active users: {$activeUsers}");
        
        return 0;
    }
}
