<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnBrandRequest extends FormRequest
{
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
            'name' => 'required|string|max:255',
            'image_url' => 'nullable|url',
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
            'name' => 'Brand Name',
            'image_url' => 'Image URL',
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
            'name.required' => 'The brand name is required.',
            'name.string' => 'The brand name must be a string.',
            'name.max' => 'The brand name cannot exceed 255 characters.',
            'image_url.url' => 'The image URL must be a valid URL.',
        ];
    }
}
