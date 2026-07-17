<?php

namespace App\Http\Controllers\FluxAdmin;

use App\Http\Controllers\Controller;
use App\Support\SpacesVault;
use App\Support\SpacesVaultAccess;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpacesVaultController extends Controller
{
    public function stream(Request $request): StreamedResponse
    {
        SpacesVaultAccess::authorize();

        try {
            $path = SpacesVault::normalizePath($request->query('path'));
        } catch (\InvalidArgumentException) {
            abort(404);
        }

        if ($path === '') {
            abort(404);
        }

        $disk = SpacesVault::disk();

        if (! $disk->exists($path)) {
            abort(404);
        }

        $disposition = $request->query('disposition') === 'inline' ? 'inline' : 'attachment';
        $filename = basename($path);

        try {
            $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        } catch (\Throwable) {
            $mime = 'application/octet-stream';
        }

        return response()->stream(function () use ($disk, $path): void {
            $stream = $disk->readStream($path);

            if (! is_resource($stream)) {
                return;
            }

            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition.'; filename="'.addslashes($filename).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }
}
