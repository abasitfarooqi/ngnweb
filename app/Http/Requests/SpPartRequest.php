<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpPartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'part_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sp_parts', 'part_number')->ignore($this->route('id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'stock_status' => ['nullable', 'string', 'max:255'],
            'price_gbp_inc_vat' => ['nullable', 'numeric', 'min:0'],
            'global_stock' => ['nullable', 'numeric', 'min:0'],
            'meta' => ['nullable'],
            'last_synced_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ];
    }
}
