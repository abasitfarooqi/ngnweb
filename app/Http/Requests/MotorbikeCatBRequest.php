<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MotorbikeCatBRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'dop' => 'required|date',
            'motorbike_id' => 'required|exists:motorbikes,id',
            'notes' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
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
