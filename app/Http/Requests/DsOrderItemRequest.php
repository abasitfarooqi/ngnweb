<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DsOrderRequest extends FormRequest
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
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'pick_up_datetime' => 'required|date',
            'note' => 'nullable|string',
            'proceed' => 'boolean',
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
            'full_name' => 'Full Name',
            'phone' => 'Phone Number',
            'address' => 'Address',
            'postcode' => 'Postcode',
            'pick_up_datetime' => 'Pickup Date & Time',
            'note' => 'Note',
            'proceed' => 'Proceed',
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
            'phone.required' => 'The phone number is required.',
            'address.required' => 'The address is required.',
            'postcode.required' => 'The postcode is required.',
            'pick_up_datetime.required' => 'The pickup date and time is required.',
        ];
    }
}
