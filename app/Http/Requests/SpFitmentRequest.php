<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpFitmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'model_id' => ['required', 'exists:sp_models,id'],
            'year' => ['required', 'string', 'max:16'],
            'country_slug' => ['required', 'string', 'max:255'],
            'country_name' => ['required', 'string', 'max:255'],
            'colour_slug' => ['required', 'string', 'max:255'],
            'colour_name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}
