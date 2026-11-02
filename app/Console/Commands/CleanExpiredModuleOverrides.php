<?php

namespace App\Console\Commands;

use App\Models\GymModuleAccess;
use Illuminate\Console\Command;

class CleanExpiredModuleOverrides extends Command
{
    protected $signature = 'modules:clean-expired';
    protected $description = 'Soft-delete expired module access overrides';

    public function handle(): int
    {
        $count = GymModuleAccess::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->whereNull('deleted_at')
            ->get()
            ->each->delete()
            ->count();

        if ($count > 0) {
            $this->warn("Cleaned {$count} expired module override(s).");
        } else {
            $this->info('No expired overrides to clean.');
        }

        return self::SUCCESS;
    }
}
