<?php

namespace App\Rules;

use App\Models\User;
use App\Services\MyServices;
use Illuminate\Contracts\Validation\Rule;

class CheckEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if($value)
        {
            $useremail = MyServices::getEncryptedString(strtolower($value));
            $email = User::where('email', $useremail)->first();
            if($email)
            {
                return false;
            }else{
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Email Address already exist.';
    }
}
