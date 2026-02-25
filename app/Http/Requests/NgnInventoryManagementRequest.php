<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NgnInventoryManagementRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check(); // Ensures only authenticated users can perform this action
    }

    public function rules()
    {
        // Start with the general rules
        $rules = [
            'branch_id' => 'required|exists:branches,id',  // Branch must exist
            'product_id' => 'required|exists:ngn_products,id',  // Product must exist
            'transaction_date' => 'required|date',  // Valid date is required
            'in' => 'nullable|numeric|min:0',  // In stock should be numeric and non-negative
            'out' => 'nullable|numeric|min:0',  // Out stock should be numeric and non-negative
            'transaction_type' => 'required|in:stock_transfer,stock_purchase,shop_sale,online_sale,stock_adjustment,opening_stock', // Must be one of the valid types
            'remarks' => 'nullable|string|max:255',  // Optional remarks field
        ];

        // Check if the transaction type is stock_transfer and add case-specific rules
        if ($this->input('transaction_type') === 'stock_transfer') {
            $rules['from_branch_id'] = 'required|exists:branches,id|different:to_branch_id'; // From branch must be required and different from to branch
            $rules['to_branch_id'] = 'required|exists:branches,id';  // To branch must be required
            $rules['transfer_qty'] = 'required|numeric|min:1'; // Transfer quantity must be required and positive
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'branch_id' => 'Branch',
            'product_id' => 'Product',
            'transaction_date' => 'Transaction Date',
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'transaction_type' => 'Transaction Type',
            'remarks' => 'Remarks',
            'from_branch_id' => 'Sending Branch',
            'to_branch_id' => 'Receiving Branch',
            'transfer_qty' => 'Transfer Quantity',
        ];
    }

    public function messages()
    {
        $messages = [
            'branch_id.required' => 'The branch is required.',
            'product_id.required' => 'The product is required.',
            'transaction_date.required' => 'The transaction date is required.',
            'in.numeric' => 'The stock in must be a valid number.',
            'out.numeric' => 'The stock out must be a valid number.',
            'transaction_type.required' => 'Please select a valid transaction type.',
        ];

        if ($this->input('transaction_type') === 'stock_transfer') {
            $rules['from_branch_id'] = 'required|exists:branches,id|different:to_branch_id'; // Ensure 'from_branch_id' is required for stock transfers
            $rules['to_branch_id'] = 'required|exists:branches,id';  // Ensure 'to_branch_id' is required for stock transfers
        } else {
            // Ensure that 'from_branch_id' and 'to_branch_id' are not required for other transaction types
            $rules['from_branch_id'] = 'nullable|exists:branches,id';
            $rules['to_branch_id'] = 'nullable|exists:branches,id';
        }

        return $messages;
    }
}
