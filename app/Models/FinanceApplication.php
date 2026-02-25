<?php

namespace App\Models;

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
        'is_used',
        'is_used_extended',
        'is_used_extended_custom',
        'is_new_latest',
        'is_used_latest',
        'is_subscription',
        'subscription_option',
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
            ->with(['application_items' => function ($items) {
                $items->with('motorbike:id,reg_no,make,model');
            }]);
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

    protected static function boot()
    {
        parent::boot();

        static::saved(function (FinanceApplication $financeApplication) {
            // Log the boolean flags explicitly

            \Log::info($financeApplication);
            // dd($financeApplication);
            app(\App\Http\Controllers\Admin\FinanceApplicationCrudController::class)
                ->generateAgreementAccess($financeApplication);
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
