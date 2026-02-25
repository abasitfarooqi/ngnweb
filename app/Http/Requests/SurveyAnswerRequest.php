<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyAnswerRequest extends FormRequest
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
            'response_id' => 'required|exists:ngn_survey_responses,id',
            'question_id' => 'required|exists:ngn_survey_questions,id',
            'option_id' => 'nullable|exists:ngn_survey_options,id',
            'answer_text' => 'nullable|string',
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
            'response_id.required' => 'The response selection is required.',
            'response_id.exists' => 'The selected response does not exist.',
            'question_id.required' => 'The question selection is required.',
            'question_id.exists' => 'The selected question does not exist.',
            'option_id.exists' => 'The selected option does not exist.',
            'answer_text.string' => 'The answer text must be a string.',
        ];
    }
}
