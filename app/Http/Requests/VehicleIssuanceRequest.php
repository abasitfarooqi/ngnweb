<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleIssuanceRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'issue_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'motorbike_id' => 'required|exists:motorbikes,id',
            'notes' => 'required|string',
            'is_returned' => 'required|boolean',
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
    //
}
