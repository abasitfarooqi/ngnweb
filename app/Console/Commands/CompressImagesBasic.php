<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CompressImagesBasic extends Command
{
    protected $signature = 'images:basic-compress {path?} {--width=1200} {--quality=75}';

    protected $description = 'Compress images in a directory using Intervention (resize + quality adjustment)';

    public function handle()
    {
        $folder = $this->argument('path') ?? public_path('assets/images');
        $maxWidth = (int) $this->option('width');
        $quality = (int) $this->option('quality');

        if (! File::exists($folder)) {
            $this->error("Directory does not exist: $folder");

            return;
        }

        $images = File::allFiles($folder);
        $count = 0;

        foreach ($images as $file) {
            $ext = strtolower($file->getExtension());

            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                try {
                    $image = Image::make($file->getPathname());

                    // Resize if wider than max width
                    if ($image->width() > $maxWidth) {
                        $image->resize($maxWidth, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    $image->save($file->getPathname(), $quality);
                    $this->info("Compressed: {$file->getFilename()}");
                    $count++;
                } catch (\Exception $e) {
                    $this->warn("Failed: {$file->getFilename()} - {$e->getMessage()}");
                }
            }
        }

        $this->info("Done. Compressed $count images in $folder");
    }
}
