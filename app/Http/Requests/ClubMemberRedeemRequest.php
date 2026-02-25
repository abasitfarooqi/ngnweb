<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClubMemberRedeemRequest extends FormRequest
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
            // 'redeem_total' => 'required|numeric', // Corrected here
            'redeem_total' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $clubMemberId = $this->input('club_member_id');
                    if ($clubMemberId) {
                        $member = \App\Models\ClubMember::find($clubMemberId);
                        if ($member && $value > $member->available_redeemable_balance) {
                            $fail("The redeem amount (£{$value}) exceeds available balance (£{$member->available_redeemable_balance}).");
                        }
                    }
                },
            ],
            'note' => 'nullable|string|max:255',
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
