<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnStockHandlerRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'sku' => 'required|exists:ngn_products,sku',
            'catford_stock' => 'required|integer|min:0',
            'tooting_stock' => 'required|integer|min:0',
        ];
    }
}
