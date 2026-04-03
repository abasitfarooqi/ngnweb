<?php

namespace App\Support;

use Illuminate\Support\Facades\View;

/**
 * Builds the mailData array for emails.templates.agreement-controller-universal
 * by rendering a legacy Blade email and extracting its body inner HTML.
 */
final class UniversalMailPayload
{
    /**
     * @param  array<string, mixed>  $viewData
     * @param  array<string, mixed>  $overrides  title, subject, url, customer, greetingName, etc.
     * @return array<string, mixed>
     */
    public static function fromLegacyEmailView(string $view, array $viewData, array $overrides = []): array
    {
        $html = View::make($view, $viewData)->render();
        $bodyHtml = self::extractBodyInnerHtml($html);
        $title = (string) ($overrides['title'] ?? $overrides['subject'] ?? 'NGN Motors Update');

        return array_merge([
            'title' => $title,
            'body_html' => $bodyHtml,
        ], $overrides);
    }

    /**
     * For Mailable::build() — returns the with[] array for agreement-controller-universal.
     *
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
}
