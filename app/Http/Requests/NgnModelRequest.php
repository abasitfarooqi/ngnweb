<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\IgnoresCurrentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NgnModelRequest extends FormRequest
{
    use IgnoresCurrentRecord;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('ngn_models', 'name')->ignore($this->uniqueIgnoreId())],
            // 'image_url' => 'nullable|url',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Model Name',
            // 'image_url' => 'Model Image URL',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The model name is required.',
            'name.string' => 'The model name must be a string.',
            'name.max' => 'The model name cannot exceed 255 characters.',
            'name.unique' => 'This name is already in use.',
            // 'image_url.url' => 'The image URL must be a valid URL.',
        ];
    }
}
