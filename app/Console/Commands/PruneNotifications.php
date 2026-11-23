<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Manual cleanup of old notifications (not scheduled, no web route).
 * Usage: php artisan prune-notifications 365 --dry-run
 */
class PruneNotifications extends Command
{
    protected $signature = "prune-notifications
        {days : Delete notifications older than this many days}
        {--dry-run : Only report what would be deleted}
        {--optimize : Run OPTIMIZE TABLE notifications afterwards to give the freed disk space back}
        {--force : Do not ask for confirmation}";

    protected $description = "Manually delete notifications older than the given number of days";

    private const CHUNK = 10000;

    public function handle(): int
    {
        $days = (int) $this->argument("days");
        if ($days < 1) {
            $this->error("days must be a positive number");
            return self::INVALID;
        }

        $cutoff = Carbon::now()->subDays($days);
        $query = DB::table("notifications")->where("created_at", "<", $cutoff);
        $count = (clone $query)->count();
        $total = DB::table("notifications")->count();

        $this->info("{$count} of {$total} notifications are older than {$cutoff->toDateString()} ({$days} days)");

        if ($this->option("dry-run") || $count === 0) {
            return self::SUCCESS;
        }

        if (!$this->option("force") && !$this->confirm("Delete them? This cannot be undone")) {
            $this->line("Cancelled");
            return self::SUCCESS;
        }

        // Delete in chunks to avoid locking the table for a long time.
        $deleted = 0;
        do {
            $batch = (clone $query)->limit(self::CHUNK)->delete();
            $deleted += $batch;
            $this->output->write("\rDeleted {$deleted} / {$count}");
        } while ($batch > 0);
        $this->newLine();

        // InnoDB keeps the space of deleted rows; rebuilding the table releases it.
        if ($this->option("optimize")) {
            $this->line("Optimizing notifications table...");
            DB::statement("OPTIMIZE TABLE notifications");
        }

        $this->info("Done");

        return self::SUCCESS;
    }
}
