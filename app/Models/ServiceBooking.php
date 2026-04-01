<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_auth_id',
        'submission_context',
        'enquiry_type',
        'service_type',
        'subject',
        'description',
        'requires_schedule',
        'booking_date',
        'booking_time',
        'status',
        'fullname',
        'phone',
        'reg_no',
        'email',
    ];

    public static function inferEnquiryType(?string $serviceType, ?string $description): string
    {
        $haystack = Str::lower(trim((string) $serviceType.' '.(string) $description));

        return match (true) {
            Str::contains($haystack, ['e-bike', 'ebike', 'e bike', 'pedal-assist', 'pedal assist', 'electric bicycle']) => 'e_bike',
            Str::contains($haystack, ['rental', 'rent']) => 'rental',
            Str::contains($haystack, ['used bike']) => 'used_bike',
            Str::contains($haystack, ['new bike']) => 'new_bike',
            Str::contains($haystack, ['finance']) => 'finance',
            Str::contains($haystack, ['mot']) => 'mot',
            Str::contains($haystack, ['repair', 'service booking']) => 'service',
            Str::contains($haystack, ['delivery', 'recovery']) => 'recovery_delivery',
            default => 'general',
        };
    }
}
