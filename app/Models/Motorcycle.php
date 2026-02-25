<?php

namespace App\Models;

use App\Services\FtpSyncService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;

class Motorcycle extends Model
{
    use CrudTrait, HasFactory, Searchable;

    protected $guarded = [];

    protected $fillable = [
        'availability',
        'sale_new_price',
        'make',
        'model',
        'year',
        'colour',
        'category',
        'description',
        'engine',
        'file_name',
        'file_path',
        'type',
    ];

    protected $dates = [
        'payment_due_date',
        'next_payment_date',
        'rental_start_date',
        'payment_date',
        'tax_due_date',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'registration' => $this->registration,
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'availability' => $this->availability,
            'rental_price' => $this->rental_price,
            'engine' => $this->engine,
        ];
    }

    protected static function booted()
    {
        static::saving(function ($motorcycle) {
            if (! $motorcycle->isDirty('file_path')) {
                \Log::info('[Motorcycle Sync] Skipped: file_path not changed.');

                return;
            }

            if (! $motorcycle->file_path) {
                \Log::info('[Motorcycle Sync] Skipped: No file_path set.');

                return;
            }

            $disk = 'used_motorbikes'; // Must match Backpack disk
            $localPath = Storage::disk($disk)->path($motorcycle->file_path);

            if (! Storage::disk($disk)->exists($motorcycle->file_path)) {
                \Log::warning("[Motorcycle Sync] File not found on disk: {$localPath}");

                return;
            }

            \Log::info("[Motorcycle Sync] Starting upload for: {$localPath}");

            try {
                $syncService = app(FtpSyncService::class);
                $result = $syncService->uploadFile($localPath);

                if ($result) {
                    \Log::info("[Motorcycle Sync] ✅ Uploaded successfully: {$localPath}");
                } else {
                    \Log::error("[Motorcycle Sync] ❌ Upload failed: {$localPath}");
                }
            } catch (\Exception $e) {
                \Log::error('[Motorcycle Sync] ❌ Exception: '.$e->getMessage());
            }
        });
    }
}
