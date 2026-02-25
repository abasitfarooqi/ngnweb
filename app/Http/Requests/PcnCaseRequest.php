<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PcnCaseRequest extends FormRequest
{
    public function authorize()
    {

        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'pcn_number' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'date_of_contravention' => 'required|date',
            'time_of_contravention' => 'required',
            'motorbike_id' => 'required|integer',
            'council_link' => 'nullable|string',
            // 'customer_id' => 'required|integer',
            'isClosed' => 'boolean',
            'full_amount' => 'required|numeric',
            'reduced_amount' => 'nullable|numeric',
            'picture_url' => 'nullable|mimes:jpeg,png,gif,pdf|max:64024',
            'note' => 'nullable|string',
            'updates.*.note' => 'required|string',
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
