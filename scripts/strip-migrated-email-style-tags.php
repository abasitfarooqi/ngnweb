<?php

declare(strict_types=1);

/**
 * Removes every <style>...</style> block from migrated email fragments (idempotent).
 * php scripts/strip-migrated-email-style-tags.php
 */

$base = dirname(__DIR__).'/resources/views/livewire/agreements/migrated/emails';
if (! is_dir($base)) {
    fwrite(STDERR, "Missing: $base\n");
    exit(1);
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
);

$n = 0;
foreach ($it as $fileInfo) {
    if (! $fileInfo->isFile() || ! str_ends_with($fileInfo->getFilename(), '.blade.php')) {
        continue;
    }
    $path = $fileInfo->getPathname();
    $content = file_get_contents($path);
    if ($content === false || ! preg_match('/<style\b/i', $content)) {
        continue;
    }
    $next = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $content);
    if (! is_string($next) || $next === $content) {
        continue;
    }
    $next = preg_replace("/\n{3,}/", "\n\n", $next) ?? $next;
    file_put_contents($path, rtrim($next)."\n");
    echo "stripped $path\n";
    $n++;
}

echo $n === 0 ? "No <style> blocks found.\n" : "Done ($n files).\n";
