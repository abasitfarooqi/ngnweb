<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerAppointmentsRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            // 'appointment_date' => 'required|datetime',
            // 'customer_name' => 'required|string',
            // 'registration_number' => 'nullable|string',
            // 'contact_number' => 'nullable|string',
            // 'email' => 'nullable|email',
            // 'is_resolved' => 'boolean',
            // 'booking_reason' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            //
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
