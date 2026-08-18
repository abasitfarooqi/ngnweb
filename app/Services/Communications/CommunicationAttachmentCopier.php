<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CommunicationAttachment;
use Illuminate\Mail\Attachment as MailAttachment;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class CommunicationAttachmentCopier
{
    public function copyFromMailable(Mailable $mailable, Communication $communication): void
    {
        if (! method_exists($mailable, 'attachments')) {
            return;
        }

        try {
            $items = $mailable->attachments();
        } catch (Throwable $exception) {
            Log::warning('Transactional communication attachments could not be listed.', [
                'communication_id' => $communication->id,
                'mailable' => $mailable::class,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        if (! is_array($items) || $items === []) {
            return;
        }

        foreach (array_values($items) as $index => $attachment) {
            if (! $attachment instanceof MailAttachment) {
                continue;
            }

            try {
                $this->storeOne($communication, $attachment, $index);
            } catch (Throwable $exception) {
                Log::warning('Transactional communication attachment was not copied onto the snapshot.', [
                    'communication_id' => $communication->id,
                    'mailable' => $mailable::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function storeOne(Communication $communication, MailAttachment $attachment, int $index): void
    {
        $contents = null;

        $attachment->attachWith(
            function (string $path) use (&$contents): void {
                if (is_file($path)) {
                    $contents = file_get_contents($path);
                }
            },
            function (mixed $data) use (&$contents): void {
                $contents = is_callable($data) ? $data() : $data;
            },
        );

        if (! is_string($contents) || $contents === '') {
            return;
        }

        $filename = trim((string) ($attachment->as ?: ''));
        if ($filename === '') {
            $filename = 'attachment-'.($index + 1).'.bin';
        }

        $safeBase = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) ?: 'attachment';
        $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
        $storedName = $safeBase.'-'.Str::uuid().($extension !== '' ? '.'.$extension : '');
        $path = 'communications/'.$communication->id.'/'.$storedName;

        Storage::disk('private')->put($path, $contents);

        CommunicationAttachment::query()->create([
            'communication_id' => $communication->id,
            'uuid' => (string) Str::uuid(),
            'disk' => 'private',
            'path' => $path,
            'filename' => $filename,
            'display_name' => $filename,
            'mime_type' => $attachment->mime ?: 'application/octet-stream',
            'file_size' => strlen($contents),
            'checksum' => hash('sha256', $contents),
            'metadata' => [
                'source' => 'mailable',
            ],
        ]);
    }
}
