<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

class NgnMotorcycleTransport extends Model
{
    use HasFactory, SoftDeletes; // Include SoftDeletes trait

    protected $fillable = [
        'customer_id',
        'user_id',
        'motorbike_id',
        'collection_name',
        'collection_business_name',
        'collection_contact_number',
        'destination_address',
        'collection_address_line_1',
        'collection_address_line_2',
        'collection_address_line_3',
        'collection_postcode',
        'delivery_name',
        'delivery_business_name',
        'delivery_contact_number',
        'delivery_address',
        'delivery_address_line_1',
        'delivery_address_line_2',
        'delivery_address_line_3',
        'delivery_postcode',
        'bike_registration_number',
        'make',
        'model',
        'year',
        'mileage',
        'colour',
        'does_bike_roll',
        'are_documents_present',
        'does_bike_have_spare_parts',
        'are_keys_present',
        'specific_date',
        'delivery_speed',
        'email',
        'contact_number',
        'first_name',
        'last_name',
    ];

    public static function validate($data)
    {
        return Validator::make($data, [
            'collection_name' => 'required|string|max:255',
            'collection_contact_number' => 'required|string|max:15',
            'collection_address_line_1' => 'required|string|max:255',
            'collection_postcode' => 'required|string|max:10',
            'delivery_name' => 'required|string|max:255',
            'delivery_contact_number' => 'required|string|max:15',
            'delivery_address_line_1' => 'required|string|max:255',
            'delivery_postcode' => 'required|string|max:10',
            'bike_registration_number' => 'required|string|max:20',
            'make' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer',
            'mileage' => 'required|integer',
            'colour' => 'required|string|max:20',
            'does_bike_roll' => 'required|boolean',
            'are_documents_present' => 'required|boolean',
            'does_bike_have_spare_parts' => 'required|boolean',
            'are_keys_present' => 'required|boolean',
            'specific_date' => 'nullable|date',
            'delivery_speed' => 'required|in:7_days,72_hours,48_hours',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:15',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Customer model
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Define the optional relationship with the Motorbike model
    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }
}
