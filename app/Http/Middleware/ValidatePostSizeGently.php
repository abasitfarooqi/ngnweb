<?php

namespace App\Http\Middleware;

use App\Support\UploadLimit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePostSizeGently
{
    public function handle(Request $request, Closure $next): Response
    {
        $max = $this->postMaxBytes();
        $length = (int) $request->server('CONTENT_LENGTH', 0);

        if ($max > 0 && $length > $max) {
            return $this->tooLarge($request);
        }

        return $next($request);
    }

    private function tooLarge(Request $request): Response
    {
        $message = 'That file is too large for this server. Use a file under '.UploadLimit::label().'.';

        if ($request->expectsJson() || $request->header('X-Livewire')) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    'video' => [$message],
                    'videoFile' => [$message],
                ],
            ], 413);
        }

        $back = $request->headers->get('Referer') ?: url()->previous();

        return response(
            '<!DOCTYPE html><html lang="en-GB"><head><meta charset="utf-8"><title>File too large</title></head>'
            .'<body style="font-family:sans-serif;padding:24px;color:#111827">'
            .'<p>'.$message.'</p>'
            .'<p><a href="'.e($back).'">Go back and try a smaller file</a></p>'
            .'</body></html>',
            413
        )->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function postMaxBytes(): int
    {
        $value = trim((string) ini_get('post_max_size'));
        if ($value === '' || $value === '0') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return (int) match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
