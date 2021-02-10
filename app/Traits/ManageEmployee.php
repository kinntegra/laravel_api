<?php

namespace App\Traits;

use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait ManageEmployee
{
    private $path = Employee::PATH;

    private $store_path = Employee::STORE_PATH;

    private $associate_store_path = Associate::STORE_PATH;

    private $employee = [
        'associate_id',
        'name',
        'user_id',
        'department_id',
        'subdepartment_id',
        'designation_id',
        'profession_id',
        'supervisor_id',
        'telephone',
        'blood_group',
        'health_issue',
        'pan_no',
        'aadhar_no',
        'birth_date',
        'anniversary_date',
        'is_active',
        'is_credential_email',
        'bse_upload',
        'deactive_reason',
    ];

private $employeeUpload = [
    'photo_upload',
    'pan_upload',
    'aadhar_upload',
];

private $employeeContact = [
    'contact_name1',
    'contact_mobile1',
    'contact_email1',
    'contact_name2',
    'contact_mobile2',
    'contact_email2',
];

private $employeeCertificate = [
    'nism_va_no',
    'nism_va_validity',
    'ria_certificate_type',
    'nism_xa_no',
    'nism_xa_validity',
    'nism_xb_no',
    'nism_xb_validity',
    'cfp_no',
    'cfp_validity',
    'cwm_no',
    'cwm_validity',
    'ca_no',
    'ca_validity',
    'cs_no',
    'cs_validity',
    'course_name',
    'course_no',
    'course_validity',
];

private $employeeCertificateUpload = [
    'nism_va_upload',
    'nism_xa_upload',
    'nism_xb_upload',
    'cfp_upload',
    'cwm_upload',
    'ca_upload',
    'cs_upload',
    'course_upload',
];

private $employeeLicence = [
    'euin_name',
    'euin_no',
    'euin_validity',
];

    protected function createEmployee($request)
    {
        $data = request($this->employee);
        if($request->has('birth_date') && !empty($request->birth_date))
        {
            $data['birth_date'] = Carbon::parse($request->birth_date)->format('Y-m-d');
        }else{$data['birth_date'] = null;}

        if($request->has('anniversary_date') && !empty($request->anniversary_date))
        {
            $data['anniversary_date'] = Carbon::parse($request->anniversary_date)->format('Y-m-d');
        }else{$data['anniversary_date'] = null;}
        if(empty($request->profession_id))
        {
            $data['profession_id'] = Associate::firstWhere('id', $request->associate_id)->profession_id;
        }
        if($request->step == 7)
        {
            if(isset($request->bse_upload) && $request->bse_upload == 1)
            {
                $data['is_active'] = 1;
                $data['is_credential_email'] = 1;
            }
        }

        return $data;
    }

    protected function createEmployeeContact($request,$id)
    {
        $data = request($this->employeeContact);

        $mydata = array();
        $xArr = array();
        $myArr = '';
        foreach ($data as $key => $value)
        {
            $key = substr($key, 8);

            if(!empty($request->contact_name.$id) && !empty($request->contact_mobile.$id) && !empty($request->contact_email.$id))
            {
                if(strchr($key,$id,true)){
                    $key = rtrim($key, $id);
                    $mydata[$key] = $value;
                    $xArr['cno'] = $id;
                    if (array_key_exists("name",$mydata) && array_key_exists("mobile",$mydata) && array_key_exists("email",$mydata))
                    $myArr = array_merge($xArr,$mydata);
                }
            }
        }
        return $myArr;
    }

    protected function uploadSingleFile($request, $key ,$id, $sub_id = '')
    {
        $upload = "";

        if($request->hasFile($key)){
            $storepath = $this->associate_store_path.'/'.$request->associate_id.'/'.$this->store_path.'/'.$id;
            $upload = $this->storeFile($request->$key, $key, $storepath, $this->path, $sub_id ? $sub_id : $id);
        }
        return $upload;
    }

    protected function createEmployeeCertificate($request)
    {
        $data = request($this->employeeCertificate);
        if($request->has('nism_va_validity') && !empty($request->nism_va_validity))
        {
            $data['nism_va_validity'] = Carbon::parse($request->nism_va_validity)->format('Y-m-d');;
        }else{$data['nism_va_validity'] = null;}

        if($request->has('nism_xa_validity') && !empty($request->nism_xa_validity))
        {
            $data['nism_xa_validity'] = Carbon::parse($request->nism_xa_validity)->format('Y-m-d');;
        }else{$data['nism_xa_validity'] = null;}

        if($request->has('nism_xb_validity') && !empty($request->nism_xb_validity))
        {
            $data['nism_xb_validity'] = Carbon::parse($request->nism_xb_validity)->format('Y-m-d');;
        }else{$data['nism_xb_validity'] = null;}

        if($request->has('cfp_validity') && !empty($request->cfp_validity))
        {
            $data['cfp_validity'] = Carbon::parse($request->cfp_validity)->format('Y-m-d');;
        }else{$data['cfp_validity'] = null;}

        if($request->has('cwm_validity') && !empty($request->cwm_validity))
        {
            $data['cwm_validity'] = Carbon::parse($request->cwm_validity)->format('Y-m-d');;
        }else{$data['cwm_validity'] = null;}

        if($request->has('ca_validity') && !empty($request->ca_validity))
        {
            $data['ca_validity'] = Carbon::parse($request->ca_validity)->format('Y-m-d');;
        }else{$data['ca_validity'] = null;}

        if($request->has('cs_validity') && !empty($request->cs_validity))
        {
            $data['cs_validity'] = Carbon::parse($request->cs_validity)->format('Y-m-d');;
        }else{$data['cs_validity'] = null;}

        if($request->has('course_validity') && !empty($request->course_validity))
        {
            $data['course_validity'] = Carbon::parse($request->course_validity)->format('Y-m-d');;
        }else{$data['course_validity'] = null;}

        return $data;
    }


    protected function createEmployeeLicence($request)
    {
        $data = request($this->employeeLicence);
        if($request->has('euin_validity') && !empty($request->euin_validity))
        {
            $data['euin_validity'] = Carbon::parse($request->euin_validity)->format('Y-m-d');;
        }else{$data['euin_validity'] = null;}

        return $data;
    }

}
