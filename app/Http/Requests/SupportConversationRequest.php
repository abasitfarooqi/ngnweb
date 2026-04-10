<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupportConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'customer_auth_id' => ['nullable', 'exists:customer_auths,id'],
            'service_booking_id' => ['nullable', 'exists:service_bookings,id'],
            'assigned_backpack_user_id' => ['nullable', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'external_ai_session_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
