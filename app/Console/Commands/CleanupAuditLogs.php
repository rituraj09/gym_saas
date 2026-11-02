<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class CleanupAuditLogs extends Command
{
    protected $signature = 'audit:cleanup {--days=90 : Delete logs older than this many days}';
    protected $description = 'Delete audit log entries older than the specified number of days';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = AuditLog::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info('No audit logs older than ' . $days . ' days. Nothing to clean.');
            return self::SUCCESS;
        }

        AuditLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$count} audit log entries older than {$days} days.");

        return self::SUCCESS;
    }
}
