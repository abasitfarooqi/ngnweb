<?php

namespace App\Console\Commands;

use App\Models\MotorbikeImage;
use App\Models\MotorbikesSale;
use App\Models\Motorcycle;
use App\Support\MotorbikeMediaStorage;
use Illuminate\Console\Command;

class PromoteMotorbikeMediaToSpaces extends Command
{
    protected $signature = 'motorbike-media:promote-to-spaces {--dry-run : List paths only, do not upload}';

    protected $description = 'Upload local used/new bike images to public DO Spaces and update stored paths.';

    public function handle(): int
    {
        if (! MotorbikeMediaStorage::spacesConfigured()) {
            $this->error('DO Spaces is not configured (DO_SPACES_KEY, DO_SPACES_SECRET, DO_SPACES_BUCKET).');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $promoted = 0;
        $skipped = 0;

        $this->info($dryRun ? 'Dry run — no uploads.' : 'Promoting motorbike media to DO Spaces…');

        foreach (['image_one', 'image_two', 'image_three', 'image_four', 'video_path'] as $field) {
            MotorbikesSale::query()
                ->whereNotNull($field)
                ->where($field, '!=', '')
                ->orderBy('id')
                ->each(function (MotorbikesSale $sale) use ($field, $dryRun, &$promoted, &$skipped): void {
                    $path = (string) $sale->{$field};
                    $next = $dryRun ? MotorbikeMediaStorage::spacesKey($path) : MotorbikeMediaStorage::promoteLocalToSpaces($path);

                    if ($next === $path) {
                        $skipped++;

                        return;
                    }

                    if (! $dryRun) {
                        $sale->updateQuietly([$field => $next]);
                    }

                    $promoted++;
                    $this->line("  {$field} sale #{$sale->id}: {$path} → {$next}");
                });
        }

        Motorcycle::query()
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->orderBy('id')
            ->each(function (Motorcycle $bike) use ($dryRun, &$promoted, &$skipped): void {
                $path = (string) $bike->file_path;
                $next = $dryRun ? MotorbikeMediaStorage::spacesKey($path) : MotorbikeMediaStorage::promoteLocalToSpaces($path);

                if ($next === $path) {
                    $skipped++;

                    return;
                }

                if (! $dryRun) {
                    $bike->updateQuietly(['file_path' => $next]);
                }

                $promoted++;
                $this->line("  new stock #{$bike->id}: {$path} → {$next}");
            });

        MotorbikeImage::query()
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->orderBy('id')
            ->each(function (MotorbikeImage $image) use ($dryRun, &$promoted, &$skipped): void {
                $path = (string) $image->image_path;
                $next = $dryRun ? MotorbikeMediaStorage::spacesKey($path) : MotorbikeMediaStorage::promoteLocalToSpaces($path);

                if ($next === $path) {
                    $skipped++;

                    return;
                }

                if (! $dryRun) {
                    $image->updateQuietly(['image_path' => $next]);
                }

                $promoted++;
                $this->line("  gallery #{$image->id}: {$path} → {$next}");
            });

        $this->info("Done. Promoted: {$promoted}, unchanged: {$skipped}.");

        return self::SUCCESS;
    }
}
