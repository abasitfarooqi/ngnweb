<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnStockMovementRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Update this if you need authorization logic
    }

    public function rules()
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'transaction_date' => 'required|date',
            'product_id' => 'required|exists:ngn_products,id',
            'in' => 'nullable|numeric|min:0',
            'out' => 'nullable|numeric|min:0',
            'transaction_type' => 'required|string|max:255|in:transfer,supplier,sales,adjustment',
            'user_id' => 'required|exists:users,id',
            'ref_doc_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function attributes()
    {
        return [
            'branch_id' => 'Branch',
            'transaction_date' => 'Transaction Date',
            'product_id' => 'Product',
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transaction_type' => 'Transaction Type',
            'user_id' => 'User',
            'ref_doc_no' => 'Reference Document',
            'remarks' => 'Remarks',
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch field is required.',
            'transaction_date.required' => 'The transaction date field is required.',
            'product_id.required' => 'The product field is required.',
            'transaction_type.required' => 'The transaction type field is required.',
            'user_id.required' => 'The user field is required.',
            'transaction_type.in' => 'The selected transaction type is invalid. Please choose from transfer, supplier, sales, or adjustment.',
            'in.numeric' => 'Stock In must be a number.',
            'out.numeric' => 'Stock Out must be a number.',
            'ref_doc_no.max' => 'The reference document must not exceed 255 characters.',
            'remarks.max' => 'Remarks must not exceed 500 characters.',
        ];
    }
}
