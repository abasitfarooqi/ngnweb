<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class PcnCase extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'pcn_cases';

    protected $fillable = [
        'pcn_number',
        'user_id',
        'date_of_contravention',
        'time_of_contravention',
        'motorbike_id',
        'council_link',
        'customer_id',
        'isClosed',
        'is_police',
        'full_amount',
        'reduced_amount',
        'picture_url',
        'note',
        'is_whatsapp_sent',
        'whatsapp_last_reminder_sent_at',
        'is_sms_sent',
        'sms_last_sent_at',
        'date_of_letter_issued',
    ];

    protected $casts = [
        'isClosed' => 'boolean',
        'date_of_contravention' => 'date',
        'full_amount' => 'decimal:2',
        'reduced_amount' => 'decimal:2',
    ];

    public function showUpdates($id)
    {
        $pcnCase = PcnCase::with('updates')->findOrFail($id);

        return view('livewire.agreements.migrated.admin.pcn_case_updates.show', compact('pcnCase'));
    }

    public function getUpdatesLink()
    {
        return '<a href="'.route('pcn-case.updates', $this->id).'" class="btn btn-sm btn-link">View Updates</a>';
    }

    public function getViewUpdatesButtonAttribute()
    {
        return '<a href="'.route('crud.pcn-case-update.index', ['case_id' => $this->id]).'" class="btn btn-sm btn-link"><i class="la la-eye"></i>View Updates</a>';
    }

    public function getDaysSinceContravention()
    {
        return Carbon::parse($this->date_of_contravention)->diffInDays(Carbon::now());
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'pcn_case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updates()
    {
        return $this->hasMany(PcnCaseUpdate::class, 'case_id');
    }

    public function getHasBeenAppealedAttribute()
    {
        return $this->updates()->where('is_appealed', true)->exists();
    }

    public function getExportButton()
    {
        return '<a href="'.backpack_url('pcn-case/export').'" class="btn btn-sm btn-link" target="_blank"><i class="la la-download"></i> Export CSV</a>';
    }

    public function tolRequests()
    {
        return $this->hasMany(PcnTolRequest::class, 'pcn_case_id');
    }

    // public function requestTolButton($crud = false)
    // {
    //     $url = backpack_url('pcn-tol-request/create?update_id=' . $this->id);
    //     return '<a class="btn btn-sm btn-link" href="'.$url.'"><i class="la la-file"></i> REQUEST TOL</a>';
    // }

    // protected static function booted()
    // {
    //     \Log::info('booted');
    //     static::creating(function ($model) {
    //         \Log::info('PCN Case update creating: ' . $model);
    //         \App::make(\App\Http\Controllers\Admin\PcnCaseCrudController::class)
    //             ->insertEvent(model: $model);
    //     });

    //     static::created(function ($model) {
    //         \Log::info('PCN Case update created: ' . $model);
    //     });

    //     // static::updating(function ($model) {
    //     //     \Log::info('PCN Case update Updating: ' . $model);
    //     // });

    //     static::updated(function ($model) {
    //         \Log::info('PCN Case update Updated: ' . $model);
    //     });

    //     static::deleting(function ($model) {
    //         \Log::info('PCN Case update deleting: ' . $model);
    //     });

    //     static::deleted(function ($model) {
    //         \Log::info('PCN Case update deleted: ' . $model);
    //     });
    // }
}
