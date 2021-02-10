<?php

namespace App\Http\Requests\Client;

use App\Rules\CheckEmail;
use App\Rules\CheckMobile;
use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'mobile' => ['required'],
            'email' => ['required'],
            'address1' => ['required'],
            'address2' => ['required'],
            'city' => ['required'],
            'state' => ['required'],
            'country' => ['required'],
            'pincode' => ['required'],
        ];
    }
}
