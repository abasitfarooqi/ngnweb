<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PcnCaseUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'case_id' => 'required|integer',
            'user_id' => 'required|exists:users,id',
            'update_date' => 'required|date',
            'is_appealed' => 'boolean',
            'is_paid_by_owner' => 'boolean',
            'is_paid_by_keeper' => 'boolean',
            'is_transferred' => 'boolean',
            'is_cancled' => 'boolean',
            'additional_fee' => 'required|numeric',
            'picture_url' => 'nullable|mimes:jpeg,png,gif,pdf|max:64024',
            'note' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [];
    }

    public function messages()
    {
        return [];
    }
}
