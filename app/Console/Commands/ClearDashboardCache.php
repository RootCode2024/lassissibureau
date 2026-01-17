<?php

namespace App\Console\Commands;

use App\Services\DashboardCacheService;
use Illuminate\Console\Command;

class ClearDashboardCache extends Command
{
    protected $signature = 'dashboard:clear-cache {--user= : Clear cache for specific user ID}';

    protected $description = 'Clear dashboard cache';

    public function handle()
    {
        $userId = $this->option('user');

        if ($userId) {
            DashboardCacheService::clearForUser($userId);
            $this->info("Dashboard cache cleared for user {$userId}!");
        } else {
            DashboardCacheService::clearAll();
            $this->info('All dashboard caches cleared successfully!');
        }

        return Command::SUCCESS;
    }
}
