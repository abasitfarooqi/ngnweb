<?php

declare(strict_types=1);

/**
 * Strip full HTML documents under resources/views/livewire/agreements/migrated/emails
 * to fragment-only blades (banner + optional head <link> + <style> blocks + body inner).
 *
 * php scripts/normalise-migrated-email-fragments.php
 */

$base = dirname(__DIR__);
$root = $base.'/resources/views/livewire/agreements/migrated/emails';
$banner = '{{-- Email fragment: outer shell is agreement-controller-universal → universal → x-emails.base --}}';

if (! is_dir($root)) {
    fwrite(STDERR, "Missing directory: $root\n");
    exit(1);
}

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$converted = 0;
$skipped = 0;

foreach ($rii as $fileInfo) {
    if (! $fileInfo->isFile() || ! str_ends_with($fileInfo->getFilename(), '.blade.php')) {
        continue;
    }

    $path = $fileInfo->getPathname();
    $content = file_get_contents($path);
    if ($content === false) {
        fwrite(STDERR, "READ FAIL: $path\n");
        $skipped++;

        continue;
    }

    $bodyOpenCount = preg_match_all('/<body\b/i', $content) ?: 0;
    $bodyCloseCount = preg_match_all('/<\/body>/i', $content) ?: 0;

    if ($bodyOpenCount !== 1 || $bodyCloseCount !== 1) {
        fwrite(STDERR, "SKIP (body tags open=$bodyOpenCount close=$bodyCloseCount): $path\n");
        $skipped++;

        continue;
    }

    if (! preg_match('/<body\b/i', $content)) {
        fwrite(STDERR, "SKIP (no body): $path\n");
        $skipped++;

        continue;
    }

    if (! preg_match('/<body[^>]*>([\s\S]*)<\/body>/i', $content, $bodyMatch)) {
        fwrite(STDERR, "SKIP (body regex): $path\n");
        $skipped++;

        continue;
    }

    $bodyInner = trim($bodyMatch[1]);

    $linkBlock = '';
    if (preg_match('/<head[^>]*>([\s\S]*?)<\/head>/i', $content, $headMatch)) {
        if (preg_match_all('/<link\b[^>]*>/i', $headMatch[1], $allLinks)) {
            foreach ($allLinks[0] as $linkTag) {
                if (preg_match('/\brel\s*=\s*([\'"]?)stylesheet\1/i', $linkTag)
                    || preg_match('/\brel\s*=\s*stylesheet\b/i', $linkTag)) {
                    $linkBlock .= $linkTag."\n";
                }
            }
        }
    }
    $linkBlock = trim($linkBlock);

    preg_match_all('/<style\b[^>]*>([\s\S]*?)<\/style>/i', $content, $styleMatches);
    $styleBlocks = [];
    foreach ($styleMatches[0] as $fullTag) {
        $styleBlocks[] = trim($fullTag);
    }
    $stylesJoined = $styleBlocks === [] ? '' : implode("\n\n", $styleBlocks)."\n\n";

    $parts = [$banner];
    if ($linkBlock !== '') {
        $parts[] = $linkBlock;
    }
    if ($stylesJoined !== '') {
        $parts[] = rtrim($stylesJoined);
    }
    $parts[] = $bodyInner;

    $newContent = implode("\n\n", array_filter($parts, fn ($p) => $p !== ''));
    $newContent = rtrim($newContent)."\n";

    if ($newContent === $content) {
        fwrite(STDERR, "SKIP (unchanged): $path\n");
        $skipped++;

        continue;
    }

    file_put_contents($path, $newContent);
    echo "OK $path\n";
    $converted++;
}

echo "\nConverted: $converted, skipped: $skipped\n";
