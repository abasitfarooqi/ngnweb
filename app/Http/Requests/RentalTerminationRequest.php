<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalTerminationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'booking_id' => 'required|integer|exists:renting_bookings,id',
            'passcode' => 'required|string|min:6|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'customer_id.integer' => 'Customer ID must be a valid integer.',
            'customer_id.exists' => 'Customer ID does not exist in our records.',

            'booking_id.required' => 'Booking ID is required.',
            'booking_id.integer' => 'Booking ID must be a valid integer.',
            'booking_id.exists' => 'Booking ID does not exist in our records.',

            'passcode.required' => 'Passcode is required.',
            'passcode.string' => 'Passcode must be a valid string.',
            'passcode.min' => 'Passcode must be at least 6 characters.',
            'passcode.max' => 'Passcode must not exceed 12 characters.',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
