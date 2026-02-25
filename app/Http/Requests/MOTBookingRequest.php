<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MOTBookingRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'title' => 'nullable|string',
            'date_of_appointment' => 'nullable|date',
            // 'start' => 'nullable|date',
            'end' => 'nullable|date',
            'vehicle_registration' => 'required|string',
            'status' => 'nullable|in:pending,available,completed,cancelled,booked',
            'vehicle_chassis' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'all_day' => 'nullable|boolean',
            'customer_name' => 'required|string',
            'customer_contact' => 'required|string',
            'customer_email' => 'required|email',
            'notes' => 'required|string',
            'payment_link' => 'nullable|url',
            'is_paid' => 'nullable|boolean',
            'payment_method' => 'required|string',
            'payment_notes' => 'required|string',
            'background_color' => 'nullable|string',
            'text_color' => 'nullable|string',
            'start' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    $end = request('end');
                    $bookingId = request()->route('id');
                    if ($value && $end) {
                        $exists = \App\Models\MOTBooking::where('start', $value)
                            ->where('end', $end)
                            ->where('id', '!=', $bookingId)
                            ->exists();
                        if ($exists) {
                            $fail('This time slot is already booked.');
                        }
                    }
                },
            ],
        ];
    }

    public function attributes()
    {
        return [];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch field is required.',
            'branch_id.exists' => 'The selected branch does not exist.',
            'vehicle_registration.required' => 'The vehicle registration field is required.',
            'customer_name.required' => 'The customer name field is required.',
            'customer_contact.required' => 'The customer contact field is required.',
            'customer_email.required' => 'The customer email field is required.',
            'customer_email.email' => 'The customer email must be a valid email address.',
            'notes.required' => 'The notes field is required.',
            'payment_method.required' => 'The payment method field is required.',
            'payment_notes.required' => 'The payment notes field is required.',
        ];
    }
}
