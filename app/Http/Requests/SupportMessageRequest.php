<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'conversation_id' => ['required', 'exists:support_conversations,id'],
            'sender_type' => ['required', 'in:customer,staff,system,ai'],
            'sender_customer_auth_id' => ['nullable', 'exists:customer_auths,id'],
            'sender_user_id' => ['nullable', 'exists:users,id'],
            'body' => ['nullable', 'string'],
        ];
    }
}
