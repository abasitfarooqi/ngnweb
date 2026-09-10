<?php

namespace App\Support;

use Closure;

/**
 * Shared validation for support chat attachments (customer portal, staff API, Backpack inbox).
 */
final class SupportChatFileRules
{
    public const MAX_FILES = 5;
    public const MAX_FILE_KB = 102400; // 100 MB per attachment
    /**
     * Rules for each uploaded file in an array field (e.g. files.* or messageFiles.*).
     *
     * @return array<int|string, mixed>
     */
    public static function eachFileRule(): array
    {
        return [
            'file',
            'max:'.self::MAX_FILE_KB,
            'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,mp4,mov,webm',
            'mimetypes:image/jpeg,image/png,image/webp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,video/mp4,video/quicktime,video/webm',
            function (string $attribute, mixed $value, Closure $fail): void {
                $mime = (string) $value->getMimeType();
                if (str_starts_with($mime, 'image/') && @getimagesize($value->getRealPath()) === false) {
                    $fail('The uploaded image is not a valid image file.');
                }
            },
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function arrayWithFiles(string $key = 'files', int $maxFiles = self::MAX_FILES): array
    {
        return [
            $key => ['nullable', 'array', 'max:'.$maxFiles],
            $key.'.*' => self::eachFileRule(),
        ];
    }
}
