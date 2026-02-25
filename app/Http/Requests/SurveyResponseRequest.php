<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyResponseRequest extends FormRequest
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
            'survey_id' => 'required|exists:ngn_surveys,id',
            'customer_id' => 'nullable|exists:customers,id',
            'club_member_id' => 'nullable|exists:club_members,id',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_contact_opt_in' => 'boolean',
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
            //
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'survey_id.required' => 'The survey selection is required.',
            'survey_id.exists' => 'The selected survey does not exist.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'club_member_id.exists' => 'The selected club member does not exist.',
            'contact_name.string' => 'The contact name must be a string.',
            'contact_name.max' => 'The contact name may not be greater than 255 characters.',
            'contact_email.email' => 'The contact email must be a valid email address.',
            'contact_email.max' => 'The contact email may not be greater than 255 characters.',
            'contact_phone.string' => 'The contact phone must be a string.',
            'contact_phone.max' => 'The contact phone may not be greater than 20 characters.',
            'is_contact_opt_in.boolean' => 'The contact opt-in must be true or false.',
        ];
    }
}
