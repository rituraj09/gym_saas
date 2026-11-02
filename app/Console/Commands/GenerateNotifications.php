<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class GenerateNotifications extends Command
{
    protected $signature = 'notifications:generate';
    protected $description = 'Generate daily notifications for expiring memberships and subscriptions';

    public function handle(NotificationService $service): int
    {
        $this->info('Generating daily notifications...');

        $stats = $service->generateAllDailyNotifications();

        $this->info("Membership notifications: {$stats['memberships']}");
        $this->info("Subscription notifications: {$stats['subscriptions']}");
        $this->info('Done.');

        return self::SUCCESS;
    }
}
