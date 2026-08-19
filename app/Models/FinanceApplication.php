<?php

namespace App\Models;

use App\Support\FluxAdminDashboardStats;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class FinanceApplication extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'finance_applications';

    protected $fillable = [
        'customer_id',
        'user_id',
        'sold_by', // <-- new field
        'is_posted',
        'deposit',
        'notes',
        'contract_date',
        'first_instalment_date',
        'weekly_instalment',
        'log_book_sent',
        'motorbike_price',
        'extra_items',
        'extra',
        'reason_of_cancellation',
        'is_cancelled',
        'logbook_transfer_date',
        'cancelled_at',
        'is_monthly',
        'is_new',
        'is_used',
        'is_used_extended',
        'is_used_extended_custom',
        'is_new_latest',
        'is_used_latest',
        'is_subscription',
        'subscription_option',
        'subs_payment_date',
    ];

    protected $casts = [
        'is_posted' => 'boolean',
        'is_cancelled' => 'boolean',
        'is_monthly' => 'boolean',
        'is_new' => 'boolean',
        'is_used' => 'boolean',
        'is_used_extended' => 'boolean',
        'is_used_extended_custom' => 'boolean',
        'is_new_latest' => 'boolean',
        'is_used_latest' => 'boolean',
        'is_subscription' => 'boolean',
        'log_book_sent' => 'boolean',
        'contract_date' => 'datetime',
        'first_instalment_date' => 'date',
        'logbook_transfer_date' => 'date',
        'cancelled_at' => 'datetime',
        'motorbike_price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'extra' => 'decimal:2',
        'weekly_instalment' => 'decimal:2',
    ];

    public function judopaySubscription()
    {
        return $this->morphOne(JudopaySubscription::class, 'subscribable');
    }

    public static function getActiveFinanceApplications()
    {
        return static::where('is_posted', true)
            ->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            })
            ->where(function ($q) {
                $q->where('log_book_sent', false)->orWhereNull('log_book_sent');
            })
            ->whereNull('logbook_transfer_date')
            ->with(['application_items' => function ($items) {
                $items->with('motorbike:id,reg_no,make,model');
            }]);
    }

    /**
     * Active finance contract (dashboard / listings):
     * - status active = not cancelled
     * - not posted (is_posted true = fully sold → not active)
     */
    public function scopeActiveContract($query)
    {
        return $query
            ->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            })
            ->where(function ($q) {
                $q->whereNull('is_posted')->orWhere('is_posted', 0);
            });
    }

    /** Listed as active: not cancelled, log book not sent, logbook not transferred. */
    public function scopeActivePaymentPlan($query)
    {
        return $query
            ->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            })
            ->where(function ($q) {
                $q->where('log_book_sent', false)->orWhereNull('log_book_sent');
            })
            ->whereNull('logbook_transfer_date');
    }

    public function isActivePaymentPlan(): bool
    {
        return ! (bool) $this->is_cancelled
            && ! (bool) $this->log_book_sent
            && $this->logbook_transfer_date === null;
    }

    /** Matches flux-admin finance index with ?status=active (posted applications only). */
    public static function activePaymentPlanListedCount(): int
    {
        return static::query()
            ->activePaymentPlan()
            ->where('is_posted', true)
            ->count();
    }

    public function application_items()
    {
        return $this->hasMany('App\Models\ApplicationItem', 'application_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ApplicationItem::class, 'application_id');
    }

    public function extraItems()
    {
        return $this->hasMany(ContractExtraItem::class, 'application_id');
    }

    public function customerContracts()
    {
        return $this->hasMany(CustomerContract::class, 'application_id');
    }

    public function hasLatestContractType(): bool
    {
        return (bool) $this->is_new_latest || (bool) $this->is_used_latest;
    }

    public function usesObsoleteContractFlags(): bool
    {
        return (bool) $this->is_used
            || (bool) $this->is_used_extended
            || (bool) $this->is_used_extended_custom;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (FinanceApplication $financeApplication) {
            if ($financeApplication->hasLatestContractType()) {
                $financeApplication->is_used = false;
                $financeApplication->is_used_extended = false;
                $financeApplication->is_used_extended_custom = false;
            }
        });

        static::saved(function (FinanceApplication $financeApplication) {
            FluxAdminDashboardStats::clearCache();

            if (request()->attributes->get('skip_finance_agreement_generation')) {
                return;
            }

            // Log the boolean flags explicitly

            \Log::info($financeApplication);
            // dd($financeApplication);
            app(\App\Http\Controllers\Admin\FinanceApplicationCrudController::class)
                ->generateAgreementAccess($financeApplication);
        });

        static::deleted(function () {
            FluxAdminDashboardStats::clearCache();
        });

        static::updating(function ($model) {
            if ($model->isDirty('sold_by')) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'sold_by' => ['The "Person who sold the bike" cannot be modified once set.'],
                ]);
            }
        });
    }
}
