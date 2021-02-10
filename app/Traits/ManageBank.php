<?php

namespace App\Traits;

trait ManageBank
{

    private $bankData = [
        'ifsc_no',
        'bank_name',
        'branch_name',
        'micr',
        'account_type',
        'account_no',
    ];

    private $bankOtherData = [
        'mfd_ria_ifsc_no',
        'mfd_ria_bank_name',
        'mfd_ria_branch_name',
        'mfd_ria_micr',
        'mfd_ria_account_type',
        'mfd_ria_account_no',
    ];

    protected function createBank($request)
    {
        $data = request($this->bankData);

        return $data;
    }

    protected function createOtherBank($request)
    {
        $data = request($this->bankOtherData);
        $mydata = [];
        foreach($data as $key => $value)
        {
            $key = substr($key, 8);
            $mydata[$key] = $value;
        }
        return $mydata;
    }
}
