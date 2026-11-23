<?php

namespace App\Console\Commands;

use App\Models\Address;
use App\Models\Event;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Merges addresses that are the same place (within a few meters).
 *
 * Imports used to create one address per event even at the same venue
 * (exact float matching never matched), so several markers stacked on the
 * map and hid each other's events. The oldest address of each group is kept,
 * events and users are moved to it, and the others are deleted.
 *
 * Usage: php artisan merge-duplicate-addresses --dry-run
 */
class MergeDuplicateAddresses extends Command
{
    protected $signature = "merge-duplicate-addresses
        {--meters= : Merge addresses closer than this (default: Address::SAME_PLACE_METERS)}
        {--dry-run : Only report what would be merged}";

    protected $description = "Merge addresses that are the same place on the map";

    public function handle(): int
    {
        $meters = (float) ($this->option("meters") ?: Address::SAME_PLACE_METERS);
        $dryRun = (bool) $this->option("dry-run");
        $merged = 0;
        $groups = 0;
        $mergedIds = [];

        foreach (Address::orderBy("id")->cursor() as $address) {
            // Already merged into an earlier address in this run
            if (isset($mergedIds[$address->id])) {
                continue;
            }

            $duplicates = Address::query()
                ->where("id", ">", $address->id)
                ->whereBetween("latitude", [
                    $address->latitude - $meters / 111_000,
                    $address->latitude + $meters / 111_000,
                ])
                ->get()
                ->filter(
                    fn(Address $other) => !isset($mergedIds[$other->id]) &&
                        $address->distanceToKM($other) * 1000 <= $meters,
                );

            if ($duplicates->isEmpty()) {
                continue;
            }

            $groups++;
            foreach ($duplicates as $duplicate) {
                $mergedIds[$duplicate->id] = true;
            }
            $merged += $duplicates->count();
            $this->line(sprintf(
                "#%d %s <- %s",
                $address->id,
                $address->name,
                $duplicates->map(fn(Address $d) => "#{$d->id}")->implode(", "),
            ));

            if ($dryRun) {
                continue;
            }

            DB::transaction(function () use ($address, $duplicates) {
                $ids = $duplicates->pluck("id");
                Event::withoutGlobalScopes()
                    ->whereIn("address_id", $ids)
                    ->update(["address_id" => $address->id]);
                User::whereIn("address_id", $ids)->update([
                    "address_id" => $address->id,
                ]);
                if (!$address->country_id) {
                    $country = $duplicates->firstWhere("country_id", "!=", null);
                    if ($country) {
                        $address->update(["country_id" => $country->country_id]);
                    }
                }
                Address::whereIn("id", $ids)->delete();
            });
        }

        $summary = ($dryRun ? "[dry run] " : "") .
            "{$merged} duplicate addresses merged into {$groups} places (within {$meters} m)";
        Log::info($summary);
        $this->info($summary);

        return self::SUCCESS;
    }
}
