<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnProductManagementRequest extends FormRequest
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
        $rules = [
            'sku' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'normal_price' => 'required|numeric',
            'global_stock' => 'nullable|numeric',
            'brand_id' => 'required|exists:ngn_brands,id',
            'category_id' => 'required|exists:ngn_categories,id',
            'model_id' => 'required|exists:ngn_models,id',
            'new_brand' => 'nullable|string|max:255',
            'new_category' => 'nullable|string|max:255',
            'new_model' => 'nullable|string|max:255',
            'branch_id.*' => 'required|exists:branches,id', // Ensure it's required and exists in the branches table
            'sorting_code' => 'nullable|string',
        ];

        // Add unique rule conditionally
        if ($this->isMethod('post')) {
            // Creation: SKU must be unique
            $rules['sku'] .= '|unique:ngn_products,sku';
        } else {
            // Update: Exclude the current product's SKU from uniqueness check
            $rules['sku'] .= '|unique:ngn_products,sku,'.$this->route('id'); // Assuming 'id' is the route parameter for the product ID
        }

        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'sku' => 'SKU',
            'name' => 'Product Name',
            'description' => 'Description',
            'normal_price' => 'Price',
            'global_stock' => 'Global Stock',
            'brand_id' => 'Brand',
            'category_id' => 'Category',
            'model_id' => 'Model',
            'new_brand' => 'New Brand',
            'new_category' => 'New Category',
            'new_model' => 'New Model',
        ];
    }

    public function messages()
    {
        return [
            'sku.required' => 'The SKU field is required.',
            'name.required' => 'The Product Name field is required.',
            // Add other custom messages if needed
        ];
    }
}
