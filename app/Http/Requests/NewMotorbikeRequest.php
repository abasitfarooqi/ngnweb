<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewMotorbikeRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'purchase_date' => 'required|date',
            'VRM' => 'nullable|string',
            'make' => 'required|string',
            'model' => 'required|string',
            'colour' => 'required|string',
            'engine' => 'required|string',
            'year' => 'required|string',
            'VIM' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|string',
            'is_vrm' => 'required|boolean',
            'is_migrated' => 'required|boolean',
            'migrated_at' => 'nullable|date',
        ];
    }

    public function attributes()
    {
        return [
            // //
        ];
    }

    public function messages()
    {
        return [
            // //
        ];
    }
}
