<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogBackpackSearchDebug
{
    private const LOG_PATH = '/Users/abdulbasit/NGNWEBTONGN/.cursor/debug.log';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('POST') || ! str_ends_with($request->path(), '/search')) {
            return $response;
        }

        // #region agent log
        $status = $response->getStatusCode();
        $hasData = false;
        $dataCount = null;
        $bodyPreview = null;
        if ($status === 200 && $response->headers->get('Content-Type') && str_contains($response->headers->get('Content-Type'), 'json')) {
            $content = $response->getContent();
            if ($content) {
                $decoded = json_decode($content, true);
                $hasData = is_array($decoded) && array_key_exists('data', $decoded);
                $dataCount = $hasData && is_array($decoded['data']) ? count($decoded['data']) : null;
                $bodyPreview = is_array($decoded) ? array_keys($decoded) : null;
            }
        }
        $line = json_encode([
            'id' => 'log_'.uniqid(),
            'timestamp' => (int) (microtime(true) * 1000),
            'location' => 'LogBackpackSearchDebug',
            'message' => 'Backpack search response',
            'data' => [
                'path' => $request->path(),
                'status' => $status,
                'hasDataKey' => $hasData,
                'dataCount' => $dataCount,
                'bodyKeys' => $bodyPreview,
            ],
            'hypothesisId' => 'H1_H2',
        ])."\n";
        @file_put_contents(self::LOG_PATH, $line, LOCK_EX | FILE_APPEND);
        // #endregion

        return $response;
    }
}
