<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Only allow updates if the user is logged in
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
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'dob' => 'required|date',
            'license_issuance_date' => 'required|date',
            'license_expiry_date' => 'required|date',
            'license_issuance_authority' => 'required|string',
            'license_number' => 'required|string',
            'address' => 'required|string|min:2|max:255',
            'postcode' => 'required|string|min:2|max:10',
            'emergency_contact' => 'nullable|string|min:2|max:255',
            'whatsapp' => 'nullable|string|min:2|max:20',
            'phone' => 'required|string|min:2|max:20',
            'city' => 'required|string|min:2|max:255',
            'country' => 'required|string|min:2|max:255',
            'nationality' => 'required|string|min:2|max:255',
            'email' => 'required|email|min:2|max:255|unique:customers,email,'.$this->id,
            'reputation_note' => 'nullable|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
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
            'dob' => 'date of birth',
            'postcode' => 'postal code',
            'emergency_contact' => 'emergency contact',
            'reputation_note' => 'note',
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
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'dob.required' => 'Please provide a valid date of birth.',
            'address.required' => 'The address field cannot be empty.',
            'postcode.required' => 'Please provide a valid postal code.',
            'phone.required' => 'The phone number is required.', // Custom message for phone
            'city.required' => 'The city field is required.',
            'country.required' => 'The country field is required.',
            'nationality.required' => 'Please specify your nationality.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already in use. Please use a different email.',
            'rating.integer' => 'Rating should be an integer value between 1 and 5.',
            'rating.min' => 'The minimum rating allowed is 1.',
            'rating.max' => 'The maximum rating allowed is 5.',
        ];
    }
}
