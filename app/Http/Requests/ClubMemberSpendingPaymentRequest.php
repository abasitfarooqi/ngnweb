<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubMemberSpendingPaymentRequest extends FormRequest
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
            'club_member_id' => 'required|exists:club_members,id',
            'received_total' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $clubMemberId = $this->input('club_member_id');
                    if ($clubMemberId) {
                        $member = \App\Models\ClubMember::find($clubMemberId);
                        if ($member && $value > $member->total_unpaid_spending) {
                            $fail("The payment amount (£{$value}) exceeds total unpaid amount (£{$member->total_unpaid_spending}).");
                        }
                    }
                },
            ],
            'spending_id' => 'nullable|exists:club_member_spendings,id',
            'date' => 'required|date',
            'pos_invoice' => 'nullable|string|max:50',
            'branch_id' => 'required|string|in:CATFORD,SUTTON,TOOTING',
            'note' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
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
