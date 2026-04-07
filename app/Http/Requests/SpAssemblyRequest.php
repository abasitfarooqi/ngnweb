<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpAssemblyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'fitment_id' => ['required', 'exists:sp_fitments,id'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sp_assemblies', 'slug')
                    ->where('fitment_id', $this->input('fitment_id'))
                    ->ignore($this->route('id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'diagram_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
