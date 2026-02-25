<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JudopayCitPaymentSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware/controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
            'consumer_reference' => 'required|string|max:255',
            'judopay_payment_reference' => 'nullable|string|max:255', // Made nullable since it might be generated
            'customer_email' => 'required|email|max:255',
            'customer_mobile' => 'nullable|string|max:50',
            'customer_name' => 'required|string|max:255',
            'card_holder_name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:0.01|max:9999.99',
            'order_reference' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription ID is required',
            'subscription_id.exists' => 'Subscription not found',
            'customer_email.required' => 'Customer email is required',
            'customer_email.email' => 'Please provide a valid email address',
            'customer_name.required' => 'Customer name is required',
            'card_holder_name.required' => 'Card holder name is required',
            'address1.required' => 'Address line 1 is required',
            'city.required' => 'City is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0.01',
            'integral.max' => 'Amount cannot exceed 9999.99',
        ];
    }
}
