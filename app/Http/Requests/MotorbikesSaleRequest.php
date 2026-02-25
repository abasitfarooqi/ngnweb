<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MotorbikesSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // On update, motorbike_id and condition are disabled so they are not in the request
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'motorbike_id' => $isUpdate ? 'nullable|exists:motorbikes,id' : 'required|exists:motorbikes,id',
            'condition' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'is_sold' => 'required|boolean',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_phone' => 'nullable|string|max:255',
            'buyer_email' => 'nullable|email|max:255',
            'buyer_address' => 'nullable|string|max:1000',
            'mileage' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'engine' => 'required|string|max:255',
            'suspension' => 'required|string|max:255',
            'brakes' => 'required|string|max:255',
            'belt' => 'required|string|max:255',
            'electrical' => 'required|string|max:255',
            'tires' => 'required|string|max:255',
            'note' => 'required|string', // Required due to NOT NULL constraint
            'v5_available' => 'required|boolean',
            'image_one' => 'nullable|image|max:2048',
            'image_two' => 'nullable|image|max:2048',
            'image_three' => 'nullable|image|max:2048',
            'image_four' => 'nullable|image|max:2048',
        ];

        // When Is Sold is checked, require Buyer Name, Phone, Email (Address stays optional)
        // Backpack can send is_sold as 1, "1", "on", etc.
        $isSold = filter_var($this->input('is_sold'), FILTER_VALIDATE_BOOLEAN);
        if ($isSold) {
            $rules['buyer_name'] = 'required|string|max:255';
            $rules['buyer_phone'] = 'required|string|max:255';
            $rules['buyer_email'] = 'required|email|max:255';
        }

        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'buyer_name' => 'Buyer Name',
            'buyer_phone' => 'Buyer Phone',
            'buyer_email' => 'Buyer Email',
            'buyer_address' => 'Buyer Address',
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'motorbike_id.required' => 'The motorbike field is required.',
            'motorbike_id.exists' => 'The selected motorbike does not exist.',
            'condition.required' => 'The condition field is required.',
            'mileage.required' => 'The mileage field is required.',
            'mileage.numeric' => 'The mileage must be a number.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'note.required' => 'The note field is required.',
            'note.string' => 'The note must be a string.',
            'image_one.image' => 'The image must be an image file.',
            'image_one.max' => 'The image must not be greater than 2MB.',
            'image_two.image' => 'The image must be an image file.',
            'image_two.max' => 'The image must not be greater than 2MB.',
            'image_three.image' => 'The image must be an image file.',
            'image_three.max' => 'The image must not be greater than 2MB.',
            'image_four.image' => 'The image must be an image file.',
            'image_four.max' => 'The image must not be greater than 2MB.',
            'buyer_name.required' => 'Buyer Name is required when Is Sold is checked.',
            'buyer_phone.required' => 'Buyer Phone is required when Is Sold is checked.',
            'buyer_email.required' => 'Buyer Email is required when Is Sold is checked.',
            'buyer_email.email' => 'Buyer Email must be a valid email address (e.g. name@example.com). Please correct it and try again.',
        ];
    }
}
