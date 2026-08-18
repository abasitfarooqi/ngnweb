<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationAttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'filename' => $this->filename,
            'display_name' => $this->display_name,
            'mime_type' => $this->mime_type,
            'file_size' => $this->file_size,
            'download_url' => route('api.v1.customer.communications.attachments.show', [
                'communication' => $this->communication?->uuid,
                'attachment' => $this->uuid,
            ]),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
