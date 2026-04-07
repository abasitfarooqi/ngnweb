<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpMakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sp_makes', 'slug')->ignore($this->route('id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ];
    }
}
