<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:15',
            'pickup_date' => 'required|date',
            'pickup_address' => 'required|string|max:255',
            'dropoff_address' => 'required|string|max:255',
            'vrm' => 'required|string|max:20',
            'total_distance' => 'required|numeric',
            'surcharge' => 'required|numeric',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'vrm' => 'Vehicle Registration Number',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'full_name.required' => 'The full name is required.',
            'email.required' => 'The email address is required.',
            'phone_number.required' => 'The phone number is required.',
            'pickup_date.required' => 'The pickup date is required.',
            'pickup_address.required' => 'The pickup address is required.',
            'dropoff_address.required' => 'The dropoff address is required.',
            'vrm.required' => 'The vehicle registration number is required.',
            'total_distance.required' => 'The total distance is required.',
            'surcharge.required' => 'The surcharge is required.',
        ];
    }
}
