<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContract extends Model
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
        'application_id',
        'sent_private',
    ];

    public static function deleteContractFile($id)
    {
        $contract = self::find($id);

        if (!$contract || empty($contract->file_path)) {
            \Log::warning("No contract file found for ID {$id}");
            return false;
        }

        // ✅ Normalize stored path before checking
        $sourcePath = trim(str_replace(['storage/', 'public/'], '', $contract->file_path), '/');

        $diskPublic = \Storage::disk('public');
        $diskPrivate = \Storage::disk('private');

        \Log::info("Attempting to move contract file: {$sourcePath}");

        if (! $diskPublic->exists($sourcePath)) {
            \Log::warning("Public file not found for contract ID {$id}: {$sourcePath}");
            return false;
        }

        try {
            // Ensure the directory exists in private
            $diskPrivate->makeDirectory(dirname($sourcePath));

            // Move file content
            $diskPrivate->put($sourcePath, $diskPublic->get($sourcePath));
            $diskPublic->delete($sourcePath);

            // Update DB flag
            $contract->sent_private = true;
            $contract->save();

            \Log::info("Contract ID {$id} moved to private: {$sourcePath}");
            return true;
        } catch (\Throwable $e) {
            \Log::error("Failed moving contract ID {$id}: {$e->getMessage()}");
            return false;
        }
    }


}
