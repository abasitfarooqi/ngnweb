<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimMotorbikeRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'motorbike_id' => 'required|exists:motorbikes,id',
            'branch_id' => 'required|exists:branches,id',
            'notes' => 'required|string',
            'case_date' => 'required|date',
            'is_received' => 'boolean',
            'received_date' => 'nullable|date',
            'is_returned' => 'boolean',
            'returned_date' => 'nullable|date',
            'fullname' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function attributes()
    {
        return [
            //
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
