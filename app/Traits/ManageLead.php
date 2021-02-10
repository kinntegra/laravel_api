<?php

namespace App\Traits;

trait ManageLead
{
    private $leads = [
        'associate_id',
        'first_name',
        'last_name',
        'gender',
        'mobile',
        'email',
    ];

    public function createLead($request)
    {
        $data = request($this->leads);
        return $data;
    }
}
