<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\IgnoresCurrentRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MotorbikesRequest extends FormRequest
{
    use IgnoresCurrentRecord;
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
            'year' => 'required|integer|min:1900|max:9999',
            'vin_number' => ['nullable', 'string', 'max:50', Rule::unique('motorbikes', 'vin_number')->ignore($this->uniqueIgnoreId())],
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
            'year' => 'required|integer',
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
