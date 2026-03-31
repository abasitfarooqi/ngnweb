<?php

namespace App\Models;

use App\Notifications\CustomerResetPasswordNotification;
use App\Notifications\CustomerVerifyEmailNotification;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CustomerAuth extends Authenticatable implements CanResetPassword, MustVerifyEmail
{
    use CanResetPasswordTrait, HasApiTokens, MustVerifyEmailTrait, Notifiable;

    protected $guard = 'customer';

    protected $fillable = [
        'customer_id',
        'email',
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function profile()
    {
        // Backward-compatible accessor: profile data now lives on customers.
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        \Log::info('Sending email verification notification', [
            'customer_id' => $this->customer_id,
            'email' => $this->email,
        ]);

        try {
            $this->notify(new CustomerVerifyEmailNotification);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
