<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerDocumentReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'valid_until' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['uploaded', 'pending_review', 'approved', 'rejected', 'archived'])],
            'rejection_reason' => [
                'nullable',
                'string',
                'max:2000',
                Rule::requiredIf(fn () => ($this->input('status') ?? '') === 'rejected'),
            ],
        ];
    }
}
