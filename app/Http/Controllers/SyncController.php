<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncController extends Controller
{
    public function index()
    {
        $allowedExtensions = ['pdf', 'jpg', 'png', 'jpeg', 'docx'];

        $files = collect(Storage::allFiles())
            ->filter(function ($file) use ($allowedExtensions) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                return in_array($extension, $allowedExtensions);
            })
            ->mapWithKeys(fn ($f) => [$f => Storage::lastModified($f)]);

        // Log the files that would be returned
        Log::info('Files to be returned:', $files->toArray());

        return response()->json($files);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'path' => 'required|string',
        ]);

        $path = $request->input('path');
        $file = $request->file('file');

        // Ensure the directory exists
        Storage::makeDirectory(dirname($path));

        // Store the file in the specified path within the storage directory
        Storage::put($path, file_get_contents($file));

        // Log the file upload action
        Log::info("File uploaded to path: $path");

        return response()->json(['status' => 'ok']);
    }
}
