<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PcnTolRequestRequest extends FormRequest
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
            'update_id' => 'required|exists:pcn_case_updates,id',
            'request_date' => 'required|date',
            'status' => 'required|in:pending,sent,approved,rejected',
            'letter_sent_at' => 'nullable|date',
            'note' => 'nullable|string',
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
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
