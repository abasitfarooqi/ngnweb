<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageSyncController extends Controller
{
    public function list()
    {
        $files = collect(File::allFiles(storage_path('app')))
            ->mapWithKeys(fn ($f) => [
                str_replace(storage_path('app').'/', '', $f->getPathname()) => $f->getMTime(),
            ])
            ->filter(fn ($_, $file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']));

        return response()->json($files);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'path' => 'required|string',
        ]);

        $file = $request->file('file');
        $path = $request->input('path');
        $ext = strtolower($file->getClientOriginalExtension());

        try {
            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                $image = Image::make($file)->encode($ext, 75);

                // Storage::put($path, (string) $image); // 🔴 DEACTIVATED
                Log::info("[ImageSync] Would save compressed image: $path");
            } else {
                // Storage::put($path, file_get_contents($file)); // 🔴 DEACTIVATED
                Log::info("[ImageSync] Would save non-image file: $path");
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error("[ImageSync] Upload error for $path — ".$e->getMessage());

            return response()->json(['status' => 'error'], 500);
        }
    }
}
