<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubMemberPurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     *
     * @return bool
     */
    public function authorise()
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
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'club_member_id' => 'required|exists:club_members,id',
            'percent' => 'required|numeric',
            'total' => 'required|numeric',
            'discount' => 'required|numeric',
            'is_redeemed' => 'boolean',
            'pos_invoice' => 'nullable|string|max:255|unique:club_member_purchases,pos_invoice,'.$this->route('id'),
            'branch_id' => 'nullable|string|max:255',
            'redeem_amount' => 'nullable|numeric',
            'price' => 'nullable|numeric',
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
            'user_id' => 'User ID',
            'date' => 'Date',
            'club_member_id' => 'Club Member ID',
            'total' => 'Total Amount',
            'discount' => 'Discount Amount',
            'is_redeemed' => 'Redeemed Status',
            'pos_invoice' => 'POS Invoice',
            'branch_id' => 'Branch ID',
            'redeem_amount' => 'Redeem Amount',
            'price' => 'Price',
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
            'user_id.required' => 'The User ID is required.',
            'user_id.exists' => 'The selected User ID is invalid.',
            'date.required' => 'The Date is required.',
            'date.date' => 'The Date must be a valid date.',
            'club_member_id.required' => 'The Club Member ID is required.',
            'club_member_id.exists' => 'The selected Club Member ID is invalid.',
            'total.required' => 'The Total Amount is required.',
            'total.numeric' => 'The Total Amount must be a number.',
            'discount.required' => 'The Discount Amount is required.',
            'discount.numeric' => 'The Discount Amount must be a number.',
            'is_redeemed.boolean' => 'The Redeemed Status must be true or false.',
            'pos_invoice.string' => 'The POS Invoice must be a string.',
            'pos_invoice.max' => 'The POS Invoice may not be greater than 255 characters.',
            'pos_invoice.unique' => 'The POS Invoice must be unique.',
            'branch_id.string' => 'The Branch ID must be a string.',
            'branch_id.max' => 'The Branch ID may not be greater than 255 characters.',
            'redeem_amount.numeric' => 'The Redeem Amount must be a number.',
            'price.numeric' => 'The Price must be a number.',
        ];
    }
}
