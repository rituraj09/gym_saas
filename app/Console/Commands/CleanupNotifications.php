<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CleanupNotifications extends Command
{
    protected $signature = 'notifications:cleanup';
    protected $description = 'Delete read notifications older than 30 days';

    public function handle(NotificationService $service): int
    {
        $count = $service->cleanupOldNotifications();

        if ($count > 0) {
            $this->warn("Deleted {$count} old notification(s).");
        } else {
            $this->info('No old notifications to clean.');
        }

        return self::SUCCESS;
    }
}
