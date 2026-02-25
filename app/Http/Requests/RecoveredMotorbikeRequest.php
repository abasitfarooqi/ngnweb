<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecoveredMotorbikeRequest extends FormRequest
{
    public function authorize()
    {

        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'case_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:branches,id',
            'motorbike_id' => 'required|exists:motorbikes,id',
            'notes' => 'required|string',
            'returned_date' => 'nullable|date',
        ];
    }

    public function attributes()
    {
        return [];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
