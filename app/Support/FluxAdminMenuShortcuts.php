<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Sidebar menu items / shortcuts for Flux Admin global search.
 */
final class FluxAdminMenuShortcuts
{
    /**
     * @return list<array{label: string, group: string, url: string, keywords: string}>
     */
    public static function items(): array
    {
        return FluxAdminMenuRegistry::items();
    }

    /**
     * @return list<array{label: string, title: string, snippet: string, id: string, index_url: string, show_url: ?string, edit_url: ?string, is_menu: bool, score: int}>
     */
    public static function search(string $query, int $limit = 40): array
    {
        $term = Str::lower(trim($query));

        if (mb_strlen($term) < 2) {
            return [];
        }

        $hits = [];

        foreach (self::items() as $item) {
            $score = FluxAdminMenuRegistry::matchScore($term, $item);

            if ($score <= 0) {
                continue;
            }

            $hits[] = [
                'label' => 'Menu · '.$item['group'],
                'title' => $item['label'],
                'snippet' => $item['group'].' → '.$item['label'],
                'id' => 'menu-'.md5($item['url'].$item['label']),
                'index_url' => $item['url'],
                'show_url' => $item['url'],
                'edit_url' => null,
                'is_menu' => true,
                'score' => $score,
            ];
        }

        usort($hits, fn (array $a, array $b) => $b['score'] <=> $a['score'] ?: strcmp($a['title'], $b['title']));

        return array_slice(array_map(function (array $hit) {
            unset($hit['score']);

            return $hit;
        }, $hits), 0, $limit);
    }
}
