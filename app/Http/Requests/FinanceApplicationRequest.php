<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'contract_date' => 'required|date',
            'first_instalment_date' => 'nullable|date',
            'weekly_instalment' => 'required|numeric|min:0|decimal:0,2',
            'motorbike_price' => 'required|numeric|min:0|decimal:0,2',
            'is_posted' => 'boolean',
            'deposit' => 'required|numeric|min:0|decimal:0,2',
            'notes' => 'nullable|string',
            'extra_items' => 'nullable|string',
            'log_book_sent' => 'required|boolean',
            'logbook_transfer_date' => 'nullable|date',
            'extra' => 'nullable|numeric|min:0|decimal:0,2',
            'reason_of_cancellation' => 'nullable|string',
            'is_new' => 'boolean',
            'is_used' => 'boolean',
            'is_used_extended' => 'boolean',
            'is_used_extended_custom' => 'boolean',
            'is_new_latest' => 'boolean',
            'is_used_latest' => 'boolean',
            'is_subscription' => 'boolean',
            'subscription_option' => 'nullable|in:A,B,C,D',
            'subs_payment_date' => 'nullable|integer|min:1|max:31',
            'is_cancelled' => 'boolean',
            'cancelled_at' => 'nullable|date',
        ];
        
        return $rules;
    }

    public function attributes()
    {
        return [];
    }

    public function messages()
    {
        return [

        ];
    }
}
