<?php

namespace App\Support;

/**
 * Shared validation for support chat attachments (customer portal, staff API, Backpack inbox).
 */
final class SupportChatFileRules
{
    /**
     * Rules for each uploaded file in an array field (e.g. files.* or messageFiles.*).
     *
     * @return array<int|string, mixed>
     */
    public static function eachFileRule(): array
    {
        return [
            'file',
            'max:10240',
            'mimes:jpg,jpeg,png,webp,pdf,doc,docx,txt',
            'mimetypes:image/jpeg,image/png,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function arrayWithFiles(string $key = 'files', int $maxFiles = 5): array
    {
        return [
            $key => ['nullable', 'array', 'max:'.$maxFiles],
            $key.'.*' => self::eachFileRule(),
        ];
    }
}
