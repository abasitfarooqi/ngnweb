<?php

namespace App\Models;

use App\Support\AgreementContractStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAgreement extends Model
{
    use HasFactory;

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
        'sent_private',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $record): void {
            if (! $record->wasRecentlyCreated && ! $record->wasChanged('file_path')) {
                return;
            }

            AgreementContractStorage::scheduleArchive($record);
        });
    }
}
