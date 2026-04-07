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
    ];

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
