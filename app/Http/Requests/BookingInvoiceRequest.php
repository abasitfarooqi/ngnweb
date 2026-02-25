<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingInvoiceRequest extends FormRequest
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
            'booking_id' => 'required',
            'user_id' => 'required',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric',
            'deposit' => 'required|numeric',
            'is_posted' => 'required|boolean',
            'is_paid' => 'required|boolean',
            'paid_date' => 'nullable|date',
            'state' => 'required',
            'notes' => 'nullable',
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
            //
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
            //
        ];
    }
}
