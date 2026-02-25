<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class CompanyVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $companyVehicleId = $this->route('id'); // correct route parameter

        return [
            'custodian' => 'required|max:50',
            'motorbike_id' => [
                'required',
                'exists:motorbikes,id',
                Rule::unique('company_vehicles', 'motorbike_id')->ignore($companyVehicleId),
            ],
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
