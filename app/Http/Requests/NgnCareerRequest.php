<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnCareerRequest extends FormRequest
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
            'job_title' => 'required|string|max:255',
            'description' => 'required|string',
            'employment_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'salary' => 'nullable|string',
            'contact_email' => 'required|email',
            'job_posted' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:job_posted',
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
            'job_title' => 'job title',
            'description' => 'job description',
            'employment_type' => 'employment type',
            'location' => 'job location',
            'salary' => 'salary',
            'contact_email' => 'contact email',
            'job_posted' => 'job posted date',
            'expire_date' => 'expiration date',
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
            'job_title.required' => 'The job title is required.',
            'description.required' => 'The job description is required.',
            'employment_type.required' => 'The employment type is required.',
            'location.required' => 'The job location is required.',
            'salary.string' => 'The salary must be a valid number.',
            'contact_email.required' => 'The contact email is required.',
            'contact_email.email' => 'The contact email must be a valid email address.',
            'expire_date.after_or_equal' => 'The expire date must be a date after or equal to the job posted date.',
        ];
    }
}
