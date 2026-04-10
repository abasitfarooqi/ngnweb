<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'service_type' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:255',
            'is_dealt' => 'nullable|boolean',
            'dealt_by_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }
}
