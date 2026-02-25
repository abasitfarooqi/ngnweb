<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseUsedVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'purchase_date' => 'required',
            'full_name' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'make' => 'required',
            'year' => 'required',
            'colour' => 'required',
            'fuel_type' => 'required',
            'model' => 'required',
            'reg_no' => 'required',
            'current_mileage' => 'required',
            'vin' => 'required',
            'price' => 'required',
            'engine_number' => 'required',
            'deposit' => 'required',
            'outstanding' => 'required',
            'total_to_pay' => 'required',
            'account_name' => 'nullable',
            'sort_code' => 'nullable',
            'account_number' => 'nullable',
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
