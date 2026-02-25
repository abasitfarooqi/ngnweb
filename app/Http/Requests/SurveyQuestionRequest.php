<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyQuestionRequest extends FormRequest
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
            'question_text' => 'required|string',
            'question_type' => 'required|in:single_choice,multiple_choice,text',
            'is_required' => 'boolean',
            'order' => 'required|integer',
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
            'question_text.required' => 'The question text is required.',
            'question_text.string' => 'The question text must be a string.',
            'question_type.required' => 'The question type is required.',
            'question_type.in' => 'The selected question type is invalid.',
            'is_required.boolean' => 'The required field must be true or false.',
            'order.required' => 'The order is required.',
            'order.integer' => 'The order must be an integer.',
        ];
    }
}
