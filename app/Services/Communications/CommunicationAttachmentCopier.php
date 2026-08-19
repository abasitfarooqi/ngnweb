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
        $seen = [];

        foreach ($communication->attachments()->pluck('checksum') as $checksum) {
            if (is_string($checksum) && $checksum !== '') {
                $seen[$checksum] = true;
            }
        }

        $this->copyDeclaredAttachments($mailable, $communication, $seen);
        $this->copyMailDataPdfs($mailable, $communication, $seen);
    }

    /**
     * @param  array<string, true>  $seen
     */
    private function copyDeclaredAttachments(Mailable $mailable, Communication $communication, array &$seen): void
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
                $contents = $this->contentsFromMailAttachment($attachment);
                $filename = trim((string) ($attachment->as ?: ''));
                if ($filename === '') {
                    $filename = 'attachment-'.($index + 1).'.bin';
                }

                $this->storeBytes(
                    $communication,
                    $contents,
                    $filename,
                    $attachment->mime ?: 'application/octet-stream',
                    'mailable',
                    $seen,
                );
            } catch (Throwable $exception) {
                Log::warning('Transactional communication attachment was not copied onto the snapshot.', [
                    'communication_id' => $communication->id,
                    'mailable' => $mailable::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }

    /**
     * @param  array<string, true>  $seen
     */
    private function copyMailDataPdfs(Mailable $mailable, Communication $communication, array &$seen): void
    {
        $mailData = $this->mailDataFrom($mailable);
        if (! is_array($mailData)) {
            return;
        }

        foreach ($this->pdfFileEntries($mailData) as $index => $file) {
            $path = (string) ($file['path'] ?? '');
            if ($path === '' || ! is_file($path)) {
                continue;
            }

            $contents = file_get_contents($path);
            $filename = trim((string) ($file['name'] ?? ''));
            if ($filename === '') {
                $filename = basename($path) !== '' ? basename($path) : 'attachment-'.($index + 1).'.pdf';
            }

            $this->storeBytes($communication, $contents, $filename, 'application/pdf', 'mail_data_file', $seen);
        }

        foreach ($this->pdfObjects($mailData) as $index => $pdf) {
            if (! is_object($pdf) || ! method_exists($pdf, 'output')) {
                continue;
            }

            try {
                $contents = $pdf->output();
            } catch (Throwable) {
                continue;
            }

            $this->storeBytes(
                $communication,
                is_string($contents) ? $contents : null,
                'attachment-'.($index + 1).'.pdf',
                'application/pdf',
                'mail_data_pdf',
                $seen,
            );
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mailDataFrom(Mailable $mailable): ?array
    {
        try {
            $property = new \ReflectionProperty($mailable, 'mailData');
            $value = $property->getValue($mailable);
        } catch (Throwable) {
            return null;
        }

        return is_array($value) ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $mailData
     * @return list<array<string, mixed>>
     */
    private function pdfFileEntries(array $mailData): array
    {
        $files = $mailData['pdf_files'] ?? null;
        if (! is_array($files)) {
            return [];
        }

        $entries = [];
        foreach ($files as $file) {
            if (is_array($file)) {
                $entries[] = $file;
            }
        }

        return $entries;
    }

    /**
     * @param  array<string, mixed>  $mailData
     * @return list<mixed>
     */
    private function pdfObjects(array $mailData): array
    {
        $pdf = $mailData['pdf'] ?? null;
        if ($pdf === null) {
            return [];
        }

        return is_array($pdf) ? array_values($pdf) : [$pdf];
    }

    private function contentsFromMailAttachment(MailAttachment $attachment): ?string
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

        return is_string($contents) ? $contents : null;
    }

    /**
     * @param  array<string, true>  $seen
     */
    private function storeBytes(
        Communication $communication,
        ?string $contents,
        string $filename,
        string $mimeType,
        string $source,
        array &$seen,
    ): void {
        if (! is_string($contents) || $contents === '') {
            return;
        }

        $checksum = hash('sha256', $contents);
        if (isset($seen[$checksum])) {
            return;
        }

        $filename = trim($filename) !== '' ? $filename : 'attachment.bin';
        $safeBase = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) ?: 'attachment';
        $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
        $storedName = $safeBase.'-'.Str::uuid().($extension !== '' ? '.'.$extension : '');
        $path = 'communications/'.$communication->id.'/'.$storedName;

        Storage::disk('private')->put($path, $contents);
        $seen[$checksum] = true;

        CommunicationAttachment::query()->create([
            'communication_id' => $communication->id,
            'uuid' => (string) Str::uuid(),
            'disk' => 'private',
            'path' => $path,
            'filename' => $filename,
            'display_name' => $filename,
            'mime_type' => $mimeType !== '' ? $mimeType : 'application/octet-stream',
            'file_size' => strlen($contents),
            'checksum' => $checksum,
            'metadata' => [
                'source' => $source,
            ],
        ]);
    }
}
