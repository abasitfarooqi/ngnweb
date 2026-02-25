<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'questions.*.question_text' => 'required|string|max:255',
            'questions.*.question_type' => 'required|string|in:single_choice,multiple_choice,text',
            'questions.*.is_required' => 'boolean',
            'questions.*.options.*.option_text' => 'required|string|max:255',
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
            'title.required' => 'The survey title is required.',
            'title.string' => 'The survey title must be a string.',
            'title.max' => 'The survey title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'is_active.boolean' => 'The active status must be true or false.',
            'questions.*.question_text.required' => 'Each question must have text.',
            'questions.*.question_type.required' => 'Each question must have a type.',
            'questions.*.options.*.option_text.required' => 'Each option must have text.',
        ];
    }
}
