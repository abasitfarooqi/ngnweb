<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\IgnoresCurrentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MotorbikeCatBRequest extends FormRequest
{
    use IgnoresCurrentRecord;
    public function authorize()
    {
        return backpack_auth()->check();
    }

    public function rules()
    {
        return [
            'dop' => 'required|date',
            'motorbike_id' => ['required', 'exists:motorbikes,id', Rule::unique('motorbikes_cat_b', 'motorbike_id')->ignore($this->uniqueIgnoreId())],
            'notes' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
        ];
    }

    public function attributes()
    {
        return [
            //
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
