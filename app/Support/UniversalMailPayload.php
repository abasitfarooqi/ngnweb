<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\View;

/**
 * Builds the mailData array for emails.templates.agreement-controller-universal
 * by rendering a Blade email and extracting its body inner HTML.
 *
 * By default strips legacy &lt;style&gt; and &lt;link&gt; so the universal dark shell controls typography and colour.
 * stripLegacyEmailChrome() removes duplicate chrome: .footer blocks, imgs inside .header,
 * NGN logo / footer-logo images, and unwraps lone outer .container divs.
 */
final class UniversalMailPayload
{
    /**
     * @param  array<string, mixed>  $viewData
     * @param  array<string, mixed>  $overrides  title, subject, greetingName, strip_legacy_chrome, strip_legacy_email_styles, etc.
     * @return array<string, mixed>
     */
    public static function fromLegacyEmailView(string $view, array $viewData, array $overrides = []): array
    {
        return self::processRenderedHtml(View::make($view, $viewData)->render(), $overrides);
    }

    /**
     * Resolve migrated email blades that use dots in the filename (e.g. MOT.30days.blade.php) via filesystem path.
     *
     * @param  array<string, mixed>  $viewData
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public static function fromMigratedEmailRelative(string $relativeDot, array $viewData, array $overrides = []): array
    {
        return self::processRenderedHtml(
            self::renderMigratedEmailByRelativePath($relativeDot, $viewData),
            $overrides
        );
    }

    /**
     * @param  array<string, mixed>  $viewData
     */
    public static function renderMigratedEmailByRelativePath(string $relativeDot, array $viewData): string
    {
        $path = self::migratedEmailsPhysicalBladePath($relativeDot);
        if ($path !== null) {
            return view()->file($path, $viewData)->render();
        }

        return View::make('livewire.agreements.migrated.emails.'.$relativeDot, $viewData)->render();
    }

    public static function migratedEmailsPhysicalBladePath(string $relativeDot): ?string
    {
        $relativeDot = str_replace(['\\', '/'], '.', $relativeDot);
        $relativeDot = ltrim($relativeDot, '.');
        $base = resource_path('views/livewire/agreements/migrated/emails');

        $nested = $base.'/'.str_replace('.', DIRECTORY_SEPARATOR, $relativeDot).'.blade.php';
        if (is_file($nested)) {
            return $nested;
        }

        $literal = $base.DIRECTORY_SEPARATOR.$relativeDot.'.blade.php';
        if (is_file($literal)) {
            return $literal;
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $viewData
     * @return array{mailData: array<string, mixed>}
     */
    public static function wrap(string $legacyView, array $viewData, string $title): array
    {
        return [
            'mailData' => self::fromLegacyEmailView($legacyView, $viewData, ['title' => $title]),
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private static function processRenderedHtml(string $html, array $overrides = []): array
    {
        $stripChrome = ($overrides['strip_legacy_chrome'] ?? true) === true;
        $stripStyles = ($overrides['strip_legacy_email_styles'] ?? true) === true;

        $title = (string) ($overrides['title'] ?? $overrides['subject'] ?? 'NGN Motors Update');

        if ($stripStyles) {
            $html = preg_replace('/<style\b[^>]*>[\s\S]*?<\/style>/i', '', $html) ?? $html;
            $html = preg_replace('/<link[^>]*>/i', '', $html) ?? $html;
        }

        $headStyles = $stripStyles ? '' : self::extractHeadStyles($html);
        $bodyHtml = self::extractBodyInnerHtml($html);
        if ($headStyles !== '') {
            $bodyHtml = $headStyles.$bodyHtml;
        }
        if ($stripChrome) {
            $bodyHtml = self::stripLegacyEmailChrome($bodyHtml);
        }

        return array_merge([
            'title' => $title,
            'body_html' => trim($bodyHtml),
        ], $overrides);
    }

    public static function extractBodyInnerHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $m)) {
            return trim($m[1]);
        }

        return $html;
    }

    public static function extractHeadStyles(string $html): string
    {
        if (! preg_match_all('/<style[^>]*>([\s\S]*?)<\/style>/i', $html, $matches)) {
            return '';
        }
        $out = '';
        foreach ($matches[1] as $css) {
            $out .= '<style type="text/css">'.trim($css).'</style>';
        }

        return $out;
    }

    public static function stripLegacyEmailChrome(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return $html;
        }

        $prev = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $wrapped = '<?xml encoding="UTF-8"?><div id="email-fragment-root">'.$html.'</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " footer ")]') as $node) {
            $node->parentNode?->removeChild($node);
        }

        foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " header ")]') as $header) {
            $imgs = [];
            foreach ($header->getElementsByTagName('img') as $img) {
                $imgs[] = $img;
            }
            foreach ($imgs as $img) {
                $img->parentNode?->removeChild($img);
            }
        }

        foreach ($xpath->query('//img') as $img) {
            $src = strtolower((string) $img->getAttribute('src'));
            $class = ' '.strtolower((string) $img->getAttribute('class')).' ';
            $remove = str_contains($class, ' footer-logo ')
                || str_contains($class, ' header-logo ')
                || str_contains($src, 'ngn-motor-logo')
                || str_contains($src, 'ngn_motor_logo');
            if ($remove) {
                $img->parentNode?->removeChild($img);
            }
        }

        $root = $dom->getElementById('email-fragment-root');
        if ($root) {
            self::unwrapLoneOuterContainers($root);
        }

        if (! $root) {
            libxml_clear_errors();
            libxml_use_internal_errors($prev);

            return $html;
        }

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return trim($out);
    }

    /**
     * Unwrap direct child div.container even when <style> siblings exist (head styles prepended).
     */
    private static function unwrapLoneOuterContainers(DOMElement $root): void
    {
        for ($i = 0; $i < 8; $i++) {
            $container = null;
            foreach ($root->childNodes as $n) {
                if (! $n instanceof DOMElement) {
                    continue;
                }
                if (strtolower($n->tagName) === 'style') {
                    continue;
                }
                if (strtolower($n->tagName) !== 'div') {
                    return;
                }
                $classes = preg_split('/\s+/', trim((string) $n->getAttribute('class'))) ?: [];
                if (! in_array('container', $classes, true)) {
                    return;
                }
                if ($container !== null) {
                    return;
                }
                $container = $n;
            }
            if ($container === null) {
                return;
            }
            while ($container->firstChild) {
                $root->insertBefore($container->firstChild, $container);
            }
            $root->removeChild($container);
        }
    }
}
