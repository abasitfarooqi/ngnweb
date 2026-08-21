<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class RentingWeeklyUpdate extends Model
{
    protected $table = 'renting_weekly_updates';

    protected $fillable = [
        'booking_id',
        'invoice_id',
        'note',
        'user_id',
    ];

    /** @var array<string, mixed>|null */
    protected ?array $auditOld = null;

    protected static function booted(): void
    {
        static::saving(function (self $update): void {
            $update->note = trim((string) $update->note);

            if ($update->note === '') {
                throw ValidationException::withMessages([
                    'note' => 'Please enter a note.',
                ]);
            }

            if ($update->invoice_id) {
                $invoiceBookingId = BookingInvoice::query()
                    ->whereKey($update->invoice_id)
                    ->value('booking_id');

                if ((int) $invoiceBookingId !== (int) $update->booking_id) {
                    throw ValidationException::withMessages([
                        'invoice_id' => 'That invoice does not belong to this booking.',
                    ]);
                }
            } else {
                $update->invoice_id = null;
            }
        });

        static::created(function (self $update): void {
            self::writeLog($update, 'CREATE', null, $update->auditPayload());
        });

        static::updating(function (self $update): void {
            $update->auditOld = $update->auditPayloadFromOriginal();
        });

        static::updated(function (self $update): void {
            $old = is_array($update->auditOld) ? $update->auditOld : [];
            $new = $update->auditPayload();
            $oldDiff = [];
            $newDiff = [];

            foreach (['booking_id', 'invoice_id', 'note', 'created_at'] as $key) {
                if (($old[$key] ?? null) !== ($new[$key] ?? null)) {
                    $oldDiff[$key] = $old[$key] ?? null;
                    $newDiff[$key] = $new[$key] ?? null;
                }
            }

            if ($oldDiff === []) {
                return;
            }

            self::writeLog($update, 'UPDATE', $oldDiff, $newDiff);
        });

        static::deleting(function (self $update): void {
            self::writeLog($update, 'DELETE', $update->auditPayload(), null);
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BookingInvoice::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return array<string, mixed> */
    public function auditPayload(): array
    {
        return [
            'booking_id' => (int) $this->booking_id,
            'invoice_id' => $this->invoice_id ? (int) $this->invoice_id : null,
            'note' => (string) $this->note,
            'user_id' => $this->user_id ? (int) $this->user_id : null,
            'created_at' => optional($this->created_at)?->toDateTimeString(),
        ];
    }

    /** @return array<string, mixed> */
    public function auditPayloadFromOriginal(): array
    {
        return [
            'booking_id' => (int) $this->getOriginal('booking_id'),
            'invoice_id' => $this->getOriginal('invoice_id') ? (int) $this->getOriginal('invoice_id') : null,
            'note' => (string) $this->getOriginal('note'),
            'user_id' => $this->getOriginal('user_id') ? (int) $this->getOriginal('user_id') : null,
            'created_at' => (string) ($this->getOriginal('created_at') ?? ''),
        ];
    }

    /** @param  array<string, mixed>|null  $old  @param  array<string, mixed>|null  $new */
    protected static function writeLog(self $update, string $action, ?array $old, ?array $new): void
    {
        RentingWeeklyUpdateLog::query()->create([
            'renting_weekly_update_id' => $update->id,
            'action' => $action,
            'old_data' => $old,
            'new_data' => $new,
            'changed_by' => self::staffId(),
            'created_at' => now(),
        ]);
    }

    public static function staffId(): ?int
    {
        $user = function_exists('backpack_user') ? backpack_user() : auth()->user();

        return $user?->id ? (int) $user->id : null;
    }
}
