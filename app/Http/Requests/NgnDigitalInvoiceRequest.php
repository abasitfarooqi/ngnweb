<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnDigitalInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [

        ];
    }
}
