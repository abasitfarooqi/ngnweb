<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Model
{
    use CrudTrait;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'address',
        'postcode',
        'emergency_contact',
        'whatsapp',
        'phone',
        'city',
        'country',
        'nationality',
        'email',
        'reputation_note',
        'rating',
        'license_number',
        'license_expiry_date',
        'license_issuance_authority',
        'license_issuance_date',
        'is_register',
    ];

    protected $casts = [
        'dob' => 'date',
        'rating' => 'integer',
        'license_expiry_date' => 'date',
        'license_issuance_date' => 'date',
        'is_register' => 'boolean',
    ];

    public function judopayOnboarding(): MorphOne
    {
        return $this->morphOne(JudopayOnboarding::class, 'onboardable');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDetailAttribute()
    {
        return $this->first_name.' '.$this->last_name.' | '.$this->phone.' | '.$this->email.' | '.$this->dob.' | '.$this->address.' | '.$this->license_number;
    }

    public function getAgeAttribute()
    {
        return $this->dob->age;
    }

    public function renting_bookings()
    {
        $res = $this->hasMany(RentingBooking::class, 'customer_id');

        return $res;
    }

    public function booking_invoices()
    {
        $res = $this->hasMany(BookingInvoice::class, 'customer_id');

        return $res;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function financeApplications()
    {
        return $this->hasMany(FinanceApplication::class, 'customer_id');
    }
}
