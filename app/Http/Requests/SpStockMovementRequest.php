<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'transaction_date' => ['required', 'date'],
            'sp_part_id' => ['required', 'exists:sp_parts,id'],
            'in' => ['nullable', 'numeric', 'min:0'],
            'out' => ['nullable', 'numeric', 'min:0'],
            'transaction_type' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'ref_doc_no' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}
