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
            'weekly_instalment' => 'required|numeric',
            'motorbike_price' => 'required|numeric',
            'is_posted' => 'boolean',
            'deposit' => 'required|numeric',
            'notes' => 'nullable|string',
            'extra_items' => 'nullable|string',
            'log_book_sent' => 'required|boolean',
            'extra' => 'nullable|numeric',
            'reason_of_cancellation' => 'nullable|string',
            'is_subscription' => 'boolean',
            'subscription_option' => 'nullable|in:A,B,C,D',
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