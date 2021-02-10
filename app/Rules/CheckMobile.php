<?php

namespace App\Rules;

use App\Models\User;
use App\Services\MyServices;
use Illuminate\Contracts\Validation\Rule;

class CheckMobile implements Rule
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
            $usermobile = MyServices::getEncryptedString($value);

            $mobile = User::where('mobile', $usermobile)->first();

            if($mobile)
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
        return 'Mobile No already exist.';
    }
}
