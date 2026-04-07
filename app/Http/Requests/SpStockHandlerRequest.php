<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpStockHandlerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $base = [
            'catford_stock' => ['required', 'integer', 'min:0'],
            'tooting_stock' => ['required', 'integer', 'min:0'],
            'sutton_stock' => ['required', 'integer', 'min:0'],
        ];

        if ($this->route('id')) {
            return array_merge($base, [
                'part_number' => ['required', 'exists:sp_parts,part_number'],
            ]);
        }

        return array_merge($base, [
            'sp_part_id' => ['required', 'exists:sp_parts,id'],
        ]);
    }
}
