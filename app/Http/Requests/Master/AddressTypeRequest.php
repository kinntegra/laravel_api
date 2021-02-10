<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class AddressTypeRequest extends FormRequest
{
    use ApiResponser;
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
            'name' => 'required',
        ];
    }

    /**
     * Set the validation message that aply for the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Address type is required.',
        ];
    }

    /**
     * Return to a failed validation function
     *
     */
    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $this->showRequestError($errors, Response::HTTP_BAD_REQUEST);
    }

}
