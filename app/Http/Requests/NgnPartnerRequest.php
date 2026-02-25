<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnPartnerRequest extends FormRequest
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
            'companyname' => 'required|string|max:50',
            'company_logo' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|string|max:255',
            'fleet_size' => 'nullable|integer',
            'operating_since' => 'nullable|string|max:255',
            'is_approved' => 'boolean',
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
            'companyname' => 'Company Name',
            'company_logo' => 'Company Logo',
            'company_address' => 'Company Address',
            'company_number' => 'Company Number',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'fleet_size' => 'Fleet Size',
            'operating_since' => 'Operating Since',
            'is_approved' => 'Approval Status',
        ];
    }
}
