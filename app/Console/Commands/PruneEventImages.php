<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Image;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * One-time cleanup of images left behind by events that were already deleted.
 *
 * `delete-events` used to keep an event's image when a notification pointed to it;
 * it now deletes the image with the event, so this only clears the backlog.
 * Notifications are kept: when their image is gone they show the game icon.
 */
class PruneEventImages extends Command
{
    protected $signature = "prune-event-images
        {--dry-run : Only report what would be deleted}
        {--optimize : Run OPTIMIZE TABLE images afterwards to give the freed disk space back}";

    protected $description = "Delete images (folders + rows) of events that no longer exist";

    public function handle(): int
    {
        $dryRun = (bool) $this->option("dry-run");
        $verb = $dryRun ? "Would delete" : "Deleted";

        // 1. Image folders whose event no longer exists.
        $eventUuids = Event::withoutGlobalScopes()->pluck("uuid")->flip();
        $disk = Storage::disk("public");
        $recent = time() - 3600;
        $folderCount = 0;
        foreach ($disk->directories("events-images") as $directory) {
            // Skip folders touched in the last hour: an import running at the same
            // time may have just created the event after we listed the uuids.
            if (@filemtime($disk->path($directory)) > $recent) {
                continue;
            }
            if (!isset($eventUuids[basename($directory)])) {
                $folderCount++;
                if (!$dryRun) {
                    $disk->deleteDirectory($directory);
                }
            }
        }
        $this->info("{$verb} {$folderCount} event image folders");

        // 2. `images` rows whose event no longer exists.
        $orphanImages = Image::where("parentable_type", Event::class)
            ->whereNotIn("parentable_id", Event::withoutGlobalScopes()->select("id"));
        $imageCount = $orphanImages->count();
        if (!$dryRun) {
            $orphanImages->delete();
        }
        $this->info("{$verb} {$imageCount} orphaned image rows");

        // InnoDB keeps the space of deleted rows; rebuilding the table releases it.
        if ($this->option("optimize") && !$dryRun) {
            DB::statement("OPTIMIZE TABLE images");
            $this->info("Optimized images table");
        }

        return self::SUCCESS;
    }
}
