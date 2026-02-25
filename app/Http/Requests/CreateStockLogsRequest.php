<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// use App\Http\Requests\ValidUpload;

class CreateStockLogsRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'description' => 'required|string',
            'picture' => 'sometimes|file|image|max:32768',
            // 'color' => 'sometimes|string',
            // 'sku' => 'sometimes|string',
            'branch_id' => 'required|integer',
            'qty' => 'required|integer',
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
