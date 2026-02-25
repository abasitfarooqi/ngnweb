<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnProductRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        // Ensure unique SKU while ignoring the current product in the update operation
        $skuUniqueRule = 'required|string|max:255|unique:ngn_products,sku';
        if ($this->isMethod('put')) {
            $skuUniqueRule .= ','.$this->route('id'); // Adjust for update operation
        }

        return [
            'sku' => $skuUniqueRule,
            'ean' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'variation' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'extended_description' => 'nullable|string',
            'colour' => 'nullable|string|max:255',
            'pos_variant_id' => 'nullable|string|max:255',
            'pos_product_id' => 'nullable|string|max:255',
            'brand_id' => 'required|exists:ngn_brands,id',
            'category_id' => 'required|exists:ngn_categories,id',
            'model_id' => 'required|exists:ngn_models,id',
            'normal_price' => 'required|numeric|min:0',
            'pos_price' => 'nullable|numeric|min:0',
            'pos_vat' => 'nullable|numeric|min:0',
            'global_stock' => 'nullable|numeric|min:0',
            'vatable' => 'nullable|boolean',
            'is_oxford' => 'nullable|boolean',
            'dead' => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'sku' => 'SKU',
            'ean' => 'EAN',
            'name' => 'Product Name',
            'variation' => 'Product Variation',
            'description' => 'Description',
            'extended_description' => 'Extended Description',
            'colour' => 'Colour',
            'pos_variant_id' => 'POS Variant ID',
            'pos_product_id' => 'POS Product ID',
            'brand_id' => 'Brand',
            'category_id' => 'Category',
            'model_id' => 'Model',
            'normal_price' => 'Normal Price',
            'pos_price' => 'POS Price',
            'pos_vat' => 'POS VAT',
            'global_stock' => 'Global Stock',
            'vatable' => 'Vatable',
            'is_oxford' => 'Oxford Product',
            'dead' => 'Dead Product',
        ];
    }

    public function messages()
    {
        return [
            'sku.required' => 'The SKU is required.',
            'sku.unique' => 'The SKU must be unique.',
            'name.required' => 'The product name is required.',
            'category_id.required' => 'The category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'brand_id.required' => 'The brand is required.',
            'brand_id.exists' => 'The selected brand does not exist.',
            'model_id.required' => 'The model is required.',
            'model_id.exists' => 'The selected model does not exist.',
            'normal_price.required' => 'The normal price is required.',
            'normal_price.numeric' => 'The normal price must be a number.',
            'pos_price.numeric' => 'The POS price must be a number.',
            'pos_vat.numeric' => 'The POS VAT must be a number.',
            'global_stock.numeric' => 'The global stock must be a number.',
            'vatable.boolean' => 'The vatable field must be true or false.',
            'is_oxford.boolean' => 'The Oxford product field must be true or false.',
            'dead.boolean' => 'The dead product field must be true or false.',
        ];
    }
}
