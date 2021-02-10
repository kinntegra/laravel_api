<?php

namespace App\Traits;

trait ManageAddress
{

    private $addressData = [
        'addresstype_id',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'country',
        'pincode',
        'created_at',
        'updated_at',
    ];

    private $CorrespondenceAddressData = [
        'c_addresstype_id',
        'c_address1',
        'c_address2',
        'c_address3',
        'c_city',
        'c_state',
        'c_country',
        'c_pincode',
        'created_at',
        'updated_at',
    ];

    private $ParmanentAddressData = [
        'p_addresstype_id',
        'p_address1',
        'p_address2',
        'p_address3',
        'p_city',
        'p_state',
        'p_country',
        'p_pincode',
        'created_at',
        'updated_at',
    ];

    private $nomineeAddress = [
        'nominee_addresstype_id',
        'nominee_address1',
        'nominee_address2',
        'nominee_address3',
        'nominee_city',
        'nominee_state',
        'nominee_country',
        'nominee_pincode',
        'created_at',
        'updated_at',
    ];

    private $guardianAddress = [
        'guardian_addresstype_id',
        'guardian_address1',
        'guardian_address2',
        'guardian_address3',
        'guardian_city',
        'guardian_state',
        'guardian_country',
        'guardian_pincode',
        'created_at',
        'updated_at',
    ];

    protected function createAddress($request)
    {
        $data = request($this->addressData);

        return $data;
    }

    protected function createCorrespondenceAddress($request)
    {
        $data = request($this->CorrespondenceAddressData);
        $data['c_addresstype_id'] = 1;
        foreach ($data as $key => $value)
        {
            $array[substr($key, 2)] = $value;
        }
        return $array;
    }

    protected function createParmanentAddress($request)
    {
        $data = request($this->ParmanentAddressData);
        $data['c_addresstype_id'] = 4;
        foreach ($data as $key => $value)
        {
            $array[substr($key, 2)] = $value;
        }
        return $array;
    }

    protected function createNomineeAddress($request)
    {

        $data = request($this->nomineeAddress);
        foreach ($data as $key => $value)
        {
            $array[ltrim($key, 'nominee_')] = $value;
        }
        return $array;
    }

    protected function createGuardianAddress($request)
    {
        $data = request($this->guardianAddress);
        foreach ($data as $key => $value)
        {
            $array[substr($key, 9)] = $value;
        }
        return $array;
    }


}
