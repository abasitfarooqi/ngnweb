<?php

namespace App\Models;

use App\Support\CustomerDocumentStorage;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class CustomerDocument extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'customer_id',
        'document_type_id',
        'file_name',
        'file_path',
        'file_format',
        'document_number',
        'valid_until',
        'is_verified',
        'booking_id',
        'motorbike_id',
        'sent_private',
        'status',
        'rejection_reason',
        'reviewer_id',
        'reviewed_at',
    ];

    protected static function booted(): void
    {
        static::saving(function (CustomerDocument $doc): void {
            if (! $doc->isDirty('status')) {
                return;
            }

            $status = (string) $doc->status;

            if ($status === 'approved') {
                $doc->is_verified = true;
            } elseif ($status === 'rejected') {
                $doc->is_verified = false;
            } elseif (in_array($status, ['pending_review', 'uploaded', 'archived'], true)) {
                $doc->is_verified = false;
            }

            if (in_array($status, ['approved', 'rejected'], true)
                && function_exists('backpack_auth')
                && backpack_auth()->check()) {
                $doc->reviewer_id = backpack_user()?->id;
                $doc->reviewed_at = now();
            }
        });

        static::saved(function (CustomerDocument $doc): void {
            if ($doc->status !== 'approved' || trim((string) $doc->document_number) !== '') {
                return;
            }

            $doc->forceFill([
                'document_number' => 'NGN-CD-'.str_pad((string) $doc->id, 6, '0', STR_PAD_LEFT).'-'.now()->format('Ymd'),
            ])->saveQuietly();
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        $fromSpacesOrLocal = CustomerDocumentStorage::urlForPath($this->file_path);
        if ($fromSpacesOrLocal !== null) {
            return $fromSpacesOrLocal;
        }

        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $this->file_path), '/');
        if (str_starts_with($normalised, 'customers/')) {
            try {
                return Storage::disk('public')->url($normalised);
            } catch (\Throwable) {
                return url('/storage/'.$normalised);
            }
        }

        try {
            return Storage::disk('spaces')->url($normalised);
        } catch (\Throwable) {
            return url('/storage/'.$normalised);
        }
    }
}
