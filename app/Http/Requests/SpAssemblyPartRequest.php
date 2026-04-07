<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpAssemblyPartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'assembly_id' => ['required', 'exists:sp_assemblies,id'],
            'part_id' => [
                'required',
                'exists:sp_parts,id',
                Rule::unique('sp_assembly_parts', 'part_id')
                    ->where('assembly_id', $this->input('assembly_id'))
                    ->ignore($this->route('id')),
            ],
            'qty_used' => ['nullable', 'integer', 'min:1'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'note_override' => ['nullable', 'string'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'stock_override' => ['nullable', 'string', 'max:255'],
        ];
    }
}
