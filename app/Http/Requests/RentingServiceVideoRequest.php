<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentingServiceVideoRequest extends FormRequest
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
            // 'name' => 'required|min:5|max:255'
            'booking_id' => 'required|exists:renting_bookings,id',
            'video_path' => 'required|file|mimes:mp4,mov,avi,wmv,mkv',
            'recorded_at' => 'required|date_format:Y-m-d H:i:s',
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
