<?php

namespace App\Traits;

use App\Models\Associate\Associate;
use App\Models\Master\Commercial;
use App\Models\Master\Commercialtype;
use App\Services\Security;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ManageAssociate
{

    private $path = Associate::PATH;

    private $store_path = Associate::STORE_PATH;

    private $associate = [
        'associate_code',
        'bse_password',
        'introducer_id',
        'employee_id',
        'profession_id',
        'business_tag',
        'entitytype_id',
        'entity_name',
        'pan_no',
        'aadhar_no',
        'birth_incorp_date',
        'primary_color',
        'secondary_color',
        'gst_no',
        'gst_validity',
        'shop_est_no',
        'shop_est_validity',
        'telephone',
        'is_active',
        'is_credential_email',
        'bse_upload',
        'deactive_reason',
        'created_by',
        'updated_by',
        ];

    private $associateUpload = [
        'photo_upload',
        'pan_upload',
        'aadhar_upload',
    ];

    private $associateMoreUpload = [
        'logo_upload',
        'gst_upload',
        'shop_est_upload',
        'pd_upload',
        'pd_asl_upload',
        'pd_coi_upload',
        'co_moa_upload',
        'co_aoa_upload',
        'co_coi_upload',
        'co_asl_upload',
        'co_br_upload',
    ];

    private $associateAddressUpload = [
        'address_upload',
    ];

    private $associateBankUpload = [
        'cheque_upload',
    ];

    private $associateLicence = [
        'arn_name',
        'arn_rgn_no',
        'arn_validity',
        'euin_name',
        'euin_no',
        'euin_validity',
        'ria_name',
        'ria_rgn_no',
        'ria_validity',
    ];

    private $associateLicenceUpload = [
        'arn_upload',
        'euin_upload',
        'ria_upload',
    ];

    private $associateNominee = [
        'nominee_name',
        'nominee_birth_date',
        'is_minor',
        'nominee_mobile',
        'nominee_telephone',
        'nominee_email',
        'nominee_primary_address',
    ];

    // private $associateNomineeUpload = [
    //     '',
    // ];

    private $associateCertificate = [
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

    private $associateCertificateUpload = [
        'nism_va_upload',
        'nism_xa_upload',
        'nism_xb_upload',
        'cfp_upload',
        'cwm_upload',
        'ca_upload',
        'cs_upload',
        'course_upload',
    ];

    private $assoicateGuardian = [
        'guardian_name',
        'guardian_pan_no',
        'guardian_nominee_relation',
        'guardian_mobile',
        'guardian_telephone',
        'guardian_email',
        'guardian_primary_address',
    ];

    private $assoicateGuardianUpload = [
        'guardian_pan_upload',
    ];

    private $associateAuthorise = [
        'authorised_person1',
        'authorised_email1',
        'authorised_person2',
        'authorised_email2',
        'authorised_person3',
        'authorised_email3',
    ];

    private $manageLog = [
        'model',
        'logs',
        'ip',
        'created_by',
    ];


    protected function createAssociate($request)
    {
        $data = request($this->associate);
        if($request->has('birth_incorp_date') && !empty($request->birth_incorp_date))
        {
            if($request->status == 1)
            {
                $month = Carbon::parse($request->birth_incorp_date)->format('m');
                $date = Carbon::parse($request->birth_incorp_date)->format('d');
                $password = strtoupper(substr($request->pan_no, 0, 5)).$date.$month;
                $data['bse_password'] = Security::encryptData($password);
            }
            $data['birth_incorp_date'] = Carbon::parse($request->birth_incorp_date)->format('Y-m-d');
        }else{$data['birth_incorp_date'] = null;}

        if($request->has('gst_validity') && !empty($request->gst_validity))
        {
            $data['gst_validity'] = Carbon::parse($request->gst_validity)->format('Y-m-d');;
        }else{$data['gst_validity'] = null;}

        if($request->has('shop_est_validity') && !empty($request->shop_est_validity))
        {
            $data['shop_est_validity'] = Carbon::parse($request->shop_est_validity)->format('Y-m-d');;
        }else{$data['shop_est_validity'] = null;}

        if(empty($request->primary_color))
        {
            $data['primary_color'] = '365b58';
        }
        if(empty($request->primary_color))
        {
            $data['secondary_color'] = 'ffd8b3';
        }
        if(empty($request->associate_id))
        {
            $logindata = $this->generateloginid();
            $data['associate_code'] = $logindata;
        }
        if($request->step == 12)
        {
            if(isset($request->bse_upload) && $request->bse_upload == 1)
            {
                $data['is_active'] = 1;
                $data['is_credential_email'] = 1;
            }
        }
        return $data;
    }

    private function generateloginid()
    {
        $existingid = Associate::max('associate_code');
        if($existingid)
        {
            return $existingid + 1;
        }
        return env('ASSOCIATE_USER_PREFIX') . str_pad(env('ASSOCIATE_USER_CODE'), 2, '0', STR_PAD_LEFT);
    }

    protected function uploadMultipleFile($request, $private_path, $id)
    {
        $data = request($this->$private_path);

        $upload = array();
        foreach($data as $key => $value)
        {
            if($request->hasFile($key)){
                $upload[] = $this->storeFile($request->$key, $key, $this->store_path.'/'.$id, $this->path, $id);
            }
        }
        return $upload;
    }

    protected function uploadSingleFile($request, $key ,$id, $sub_id = '')
    {
        $upload = "";
        if($request->hasFile($key)){
            $upload = $this->storeFile($request->$key, $key, $this->store_path.'/'.$id, $this->path, $sub_id ? $sub_id : $id);
        }
        return $upload;
    }


    protected function createAssociateLicence($request)
    {
        $data = request($this->associateLicence);
        if($request->has('arn_validity') && !empty($request->arn_validity))
        {
            $data['arn_validity'] = Carbon::parse($request->arn_validity)->format('Y-m-d');;
        }else{$data['arn_validity'] = null;}

        if($request->has('euin_validity') && !empty($request->euin_validity))
        {
            $data['euin_validity'] = Carbon::parse($request->euin_validity)->format('Y-m-d');;
        }else{$data['euin_validity'] = null;}

        if($request->has('ria_validity') && !empty($request->ria_validity))
        {
            $data['ria_validity'] = Carbon::parse($request->ria_validity)->format('Y-m-d');;
        }else{$data['ria_validity'] = null;}

        return $data;
    }

    protected function createAssociateCertificate($request)
    {
        $data = request($this->associateCertificate);
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

    protected function createAssociateNominee($request)
    {
        $data = request($this->associateNominee);

        if($request->has('nominee_birth_date') && !empty($request->nominee_birth_date))
        {
            $data['nominee_birth_date'] = Carbon::parse($request->nominee_birth_date)->format('Y-m-d');;
        }else{$data['nominee_birth_date'] = null;}

        return $data;
    }

    protected function createAssoicateGuardian($request)
    {
        $data = request($this->assoicateGuardian);

        return $data;
    }

    protected function createAssociateAuthorise($request,$id)
    {
        $data = request($this->associateAuthorise);

        $mydata = array();
        $xArr = array();
        $myArr = '';
        foreach ($data as $key => $value)
        {
            $key = substr($key, 11);
            if(!empty($request->authorised_person.$id) && !empty($request->authorised_email.$id))
            {
                if(strchr($key,$id,true)){
                    $key = rtrim($key, $id);
                    $mydata[$key] = $value;
                    $xArr['aid'] = $id;
                    if (array_key_exists("person",$mydata) && array_key_exists("email",$mydata))
                    $myArr = array_merge($xArr,$mydata);
                }
            }
        }
        return $myArr;
    }

    protected function createAssociateCommercial($request)
    {
        $data = request($this->getCommercial());
        $i = 0;
        foreach($data as $k => $v){
            if($v > 0){
                $k = $k."_".$v;
                $title = ["commercial_id", "commercialtype_id", "commercial"];
                $array[] = explode('_', $k);
                $array[$i][0] = Commercial::where('field_name', $array[$i][0])->first()->id;
                $array[$i][1] = Commercialtype::where('field_name', $array[$i][1])->first()->id;
                $final_array[] = array_combine(
                    $title,
                    $array[$i]
                );
                $i++;
            }
        }
        return $final_array;
    }

    protected function getCommercial()
    {
        $commercial = Commercial::pluck('field_name');
        $commercialtype = Commercialtype::pluck('field_name');
        $data = array();
        foreach($commercial as $comm){
            foreach($commercialtype as $type){
                $data[] = $comm."_".$type;
            }
        }
        return $data;
    }


    protected function ManageAssociateLog($log,$ip,$name = '',$model = '')
    {
        $data = [];
        $data['model'] = $model;
        $data['logs'] = $log;
        $data['ip'] = $ip;
        $data['created_by'] = $name;
        return $data;
    }
}
