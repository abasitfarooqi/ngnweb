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
 * Legacy full-page blades: extractHeadStyles() prepends head style tags.
 * stripLegacyEmailChrome() removes duplicate chrome: .footer blocks, imgs inside .header,
 * NGN logo / footer-logo images, and unwraps lone outer .container divs so content matches
 * fragment-style mails. Pass strip_legacy_chrome => false to skip.
 */
final class UniversalMailPayload
{
    /**
     * @param  array<string, mixed>  $viewData
     * @param  array<string, mixed>  $overrides  title, subject, greetingName, strip_legacy_chrome, etc.
     * @return array<string, mixed>
     */
    public static function fromLegacyEmailView(string $view, array $viewData, array $overrides = []): array
    {
        $html = View::make($view, $viewData)->render();
        $headStyles = self::extractHeadStyles($html);
        $bodyHtml = self::extractBodyInnerHtml($html);
        if ($headStyles !== '') {
            $bodyHtml = $headStyles.$bodyHtml;
        }
        if (($overrides['strip_legacy_chrome'] ?? true) === true) {
            $bodyHtml = self::stripLegacyEmailChrome($bodyHtml);
        }
        $title = (string) ($overrides['title'] ?? $overrides['subject'] ?? 'NGN Motors Update');

        return array_merge([
            'title' => $title,
            'body_html' => $bodyHtml,
        ], $overrides);
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
