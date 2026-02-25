<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class RentingServiceVideo extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'booking_id',
        'video_path',
        'recorded_at',
    ];

    public function rentingBooking()
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id');
    }

    public function getCustomerNameAttribute()
    {
        if ($this->rentingBooking && $this->rentingBooking->customer) {
            return $this->rentingBooking->customer->first_name.' '.$this->rentingBooking->customer->last_name;
        }

        return '';
    }

    public function getVideoUrlAttribute()
    {
        // Remove 'public/' prefix and prepend the app URL
        $relativePath = str_replace('public/', '', $this->video_path);

        return url('/storage/'.$relativePath);
    }

    public function getCustomerNameAndBookingIdAttribute()
    {
        $customerName = $this->customer_name;
        $bookingId = $this->booking_id;

        return trim($customerName).' ('.$bookingId.')';
    }

    public function setVideoPathAttribute($value)
    {
        $attribute_name = 'video_path';
        $disk = 'public';
        $destination_path = 'renting_service_videos';

        if ($value == null) {
            $this->attributes[$attribute_name] = null;

            return;
        }

        // If it's a file, store it
        if (is_file($value)) {
            $bookingId = $this->booking_id ?? request()->input('booking_id');
            $timestamp = now()->format('Ymd_His');
            $extension = $value->getClientOriginalExtension();
            $fileName = $bookingId.'_'.$timestamp.'.'.$extension;
            $path = $value->storeAs($destination_path, $fileName, $disk);
            $this->attributes[$attribute_name] = $path;
        } else {
            // If it's already a string path, just set it
            $this->attributes[$attribute_name] = $value;
        }
    }
}
