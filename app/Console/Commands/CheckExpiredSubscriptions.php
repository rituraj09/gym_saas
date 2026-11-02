<?php

namespace App\Console\Commands;

use App\Services\SubscriptionEnforcementService;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Find and expire all subscriptions past their end date';

    public function handle(SubscriptionEnforcementService $service): int
    {
        $this->info('Checking for expired subscriptions...');

        $count = $service->expireOverdueSubscriptions();

        if ($count > 0) {
            $this->warn("Expired {$count} subscription(s).");
        } else {
            $this->info('No subscriptions to expire.');
        }

        return self::SUCCESS;
    }
}
