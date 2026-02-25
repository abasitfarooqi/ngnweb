<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactQueryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
            'is_dealt' => 'boolean',
            'dealt_by_user_id' => 'required|exists:users,id',
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
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'phone' => 'Phone',
            'message' => 'Message',
            'is_dealt' => 'Is Dealt',
            'dealt_by_user_id' => 'Dealt By User ID',
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
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'subject.required' => 'Subject is required',
            'phone.required' => 'Phone is required',
            'message.required' => 'Message is required',
            'is_dealt.required' => 'Is Dealt is required',
            'dealt_by_user_id.required' => 'Dealt By User ID is required',
        ];
    }
}
