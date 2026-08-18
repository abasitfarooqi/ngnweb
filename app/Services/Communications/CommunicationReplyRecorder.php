<?php

namespace App\Services\Communications;

use App\Events\CustomerCommunicationReplyCreated;
use App\Models\Communication;
use App\Models\CommunicationAttachment;
use App\Models\CommunicationReply;
use App\Models\CustomerAuth;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CommunicationReplyRecorder
{
    /**
     * @param  array<int, UploadedFile|TemporaryUploadedFile>  $files
     */
    public function record(
        Communication $communication,
        CustomerAuth|User $author,
        string $body,
        array $files = [],
    ): CommunicationReply {
        abort_unless($this->ready(), 503, 'Communication replies are not available yet.');
        abort_unless((bool) data_get($communication->policy_snapshot, 'reply_allowed', false), 403);

        $trimmed = trim($body);
        abort_if($trimmed === '' && $files === [], 422, 'Reply body or attachment is required.');

        $reply = CommunicationReply::query()->create([
            'communication_id' => $communication->id,
            'author_type' => $author instanceof User ? 'staff' : 'customer',
            'author_id' => $author->id,
            'body' => $trimmed !== '' ? $trimmed : '(attachment)',
        ]);

        foreach (array_values($files) as $file) {
            $this->storeReplyFile($communication, $reply, $file);
        }

        $customerAuthId = (int) ($communication->customer_auth_id
            ?: $communication->recipients()->value('customer_auth_id'));

        if ($customerAuthId > 0) {
            try {
                event(new CustomerCommunicationReplyCreated($communication, $reply, $customerAuthId));
            } catch (\Throwable) {
                // Realtime is best-effort; the stored reply is the source of truth.
            }
        }

        return $reply;
    }

    public function ready(): bool
    {
        return app(CommunicationSchema::class)->repliesReady();
    }

    private function storeReplyFile(
        Communication $communication,
        CommunicationReply $reply,
        UploadedFile|TemporaryUploadedFile $file,
    ): void {
        $original = $file->getClientOriginalName() ?: 'attachment';
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $storedName = Str::slug(pathinfo($original, PATHINFO_FILENAME)).'-'.Str::uuid()
            .($extension !== '' ? '.'.$extension : '');
        $path = 'communications/'.$communication->id.'/replies/'.$storedName;

        Storage::disk('private')->put($path, $file->get());

        CommunicationAttachment::query()->create([
            'communication_id' => $communication->id,
            'uuid' => (string) Str::uuid(),
            'disk' => 'private',
            'path' => $path,
            'filename' => $original,
            'display_name' => $original,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => (int) $file->getSize(),
            'checksum' => hash('sha256', (string) $file->get()),
            'metadata' => [
                'source' => 'reply',
                'reply_id' => $reply->id,
            ],
        ]);
    }
}
