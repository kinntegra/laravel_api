<?php

namespace App\Rules;

use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use App\Models\User;
use App\Services\MyServices;
use Illuminate\Contracts\Validation\Rule;

class CheckPanNo implements Rule
{
    protected $a_id;
    protected $e_id;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($aid, $eid)
    {
        $this->a_id = $aid;
        $this->e_id = $eid;
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
        $value = MyServices::getEncryptedString(strtoupper($value));
        $panno = User::where('username', $value)->first();

        if($panno)
        {
            if(!empty($this->a_id))
            {
                $panno = Associate::Where('pan_no',$value)->where('id', '!=',$this->a_id)->first();
            }
            if(!empty($this->e_id))
            {
                $panno = Employee::Where('pan_no',$value)->where('id', '!=',$this->e_id)->first();
            }
        }

        return $panno ? false : true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Pan No Already Exist.';
    }
}
