<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MotorbikeRepairRequest extends FormRequest
{
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'arrival_date' => 'required',
            'motorbike_id' => 'required',
            'notes' => 'required',
            'is_repaired' => 'required',
            'repaired_date' => 'nullable',
            'is_returned' => 'required',
            'returned_date' => 'nullable',
            'fullname' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'updates.*.note' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'arrival_date' => 'Arrival Date and Time',
            'motorbike_id' => 'Motorbike',
            'notes' => 'Vehicle Initial Intake Information',
            'is_repaired' => 'Repair Completed',
            'repaired_date' => 'Repaired Date',
            'is_returned' => 'Returned to Customer',
            'returned_date' => 'Returned Date and Time (if applicable)',
            'fullname' => 'Full Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'branch_id' => 'Branch',
            'user_id' => 'User',
            'updates.*.note' => 'Note',
        ];
    }

    public function messages()
    {
        return [
            'arrival_date.required' => 'The arrival date is required.',
            'motorbike_id.required' => 'Please select a motorbike.',
            'notes.required' => 'Vehicle initial intake information is required.',
            'is_repaired.required' => 'Please indicate if the repair is completed.',
            'fullname.required' => 'Full name is required.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'A phone number is required.',
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch does not exist.',
            'user_id.required' => 'User identification is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'updates.*.note.required' => 'The motorbike repair update note is required.',
        ];
    }
}
