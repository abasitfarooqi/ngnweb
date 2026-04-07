<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'make_id' => ['required', 'exists:sp_makes,id'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sp_models', 'slug')
                    ->where('make_id', $this->input('make_id'))
                    ->ignore($this->route('id')),
            ],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
