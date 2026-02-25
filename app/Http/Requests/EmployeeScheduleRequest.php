<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeScheduleRequest extends FormRequest
{
    public function authorize()
    {

        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'off_day' => 'required|date_format:d/m/Y',
            // 'off_day_d_mmm_yyyy' => 'required|date_format:d M Y',
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
