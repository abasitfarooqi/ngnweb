<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewMotorbikesForSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
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
            'sale_new_price' => 'required|numeric',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:'.date('Y'),
            'colour' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'engine' => 'nullable|string|max:255',
            'file_path' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:32768', // Updated to match controller
            'type' => 'required|in:Scooter,Standard,Super Sport,Touring,Other',
        ];
    }

    public function attributes()
    {
        return [
            'sale_new_price' => 'Sale Price',
            'make' => 'Make',
            'model' => 'Model',
            'year' => 'Year',
            'colour' => 'Colour',
            'category' => 'Category',
            'description' => 'Description',
            'engine' => 'Engine Size',
            'file_path' => 'Images', // Updated to match controller
            'file_path.*' => 'Image', // Updated to match controller
            'type' => 'Motorcycle Type',
        ];
    }

    public function messages()
    {
        return [
            'sale_new_price.required' => 'Please enter a sale price',
            'sale_new_price.numeric' => 'Sale price must be a number',
            'make.required' => 'Please enter the motorcycle make',
            'model.required' => 'Please enter the motorcycle model',
            'year.required' => 'Please enter the motorcycle year',
            'year.min' => 'Year must be 1900 or later',
            'year.max' => 'Year cannot be in the future',
            'colour.required' => 'Please enter the motorcycle colour',
            'file_path.mimes' => 'Images must be jpeg, png, jpg, gif or svg format', // Updated to match controller
            'file_path.max' => 'Images must be less than 32MB', // Updated to match controller
            'type.required' => 'Please select a motorcycle type',
            'type.in' => 'Please select a valid motorcycle type',
        ];
    }
}
