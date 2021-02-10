<?php

namespace App\Http\Requests;

use App\Rules\CheckPanNo;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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

        if($this->step == 1)
        {
            return [
                'associate_id' => ['required'],
                'name' => ['required'],
                'department_id' => ['required'],
                'subdepartment_id' => ['required'],
                'designation_id' => ['required'],
                'supervisor_id' => ['required'],
                //'profession_id' => ['required'],
            ];

        }
        elseif($this->step == 2){
            return [
                'mobile' => ['bail','required','nullable','numeric','regex:/^[6-9]\d{9}$/'],
                'telephone' => ['nullable','numeric'],
                'email' => ['bail','required','nullable','email'],
                'blood_group' => ['required'],
                'contact_name1' => ['required'],
                'contact_mobile1' => ['bail','required','nullable','numeric','regex:/^[6-9]\d{9}$/'],
                'contact_email1' => ['required','nullable','email'],
                'contact_name2' => ['required'],
                'contact_mobile2' => ['bail','required','nullable','numeric','regex:/^[6-9]\d{9}$/'],
                'contact_email2' => ['required','nullable','email'],
            ];
        }
        elseif($this->step == 3){
            return [
                'pan_no' => ['bail','required', 'regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/', new CheckPanNo('', $this->employee_id)],
                'pan_upload' => ['bail', 'required_if:employee_edit,0','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                'aadhar_no' => ['bail','required','nullable','numeric','regex:/^([0-9]){12}?$/'],
                'aadhar_upload' => ['bail', 'required_if:employee_edit,0','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                'photo_upload' => ['bail', 'required_if:employee_edit,0','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                'birth_date' => ['bail','required','date','before:today'],
                'anniversary_date' => ['bail', 'nullable','before:today'],
            ];
        }
        elseif($this->step == 4){
            return [
                'c_address1' => ['required'],
                'c_address2' => ['required'],
                'c_city' => ['required'],
                'c_state' => ['required'],
                'c_country' => ['required'],
                'c_pincode' => ['required'],
                'c_address_upload' => ['bail', 'required_if:employee_edit,0','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                'p_address1' => ['required_if:is_permanent_address,0'],
                'p_address2' => ['required_if:is_permanent_address,0'],
                'p_city' => ['required_if:is_permanent_address,0'],
                'p_state' => ['required_if:is_permanent_address,0'],
                'p_country' => ['required_if:is_permanent_address,0'],
                'p_pincode' => ['required_if:is_permanent_address,0'],
                'p_address_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
            ];

        }
        elseif($this->step == 5)
        {
            return [
                'ifsc_no' => ['required'],
                'cheque_upload' => ['bail','required_if:employee_edit,0','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                'bank_name' => ['required'],
                'branch_name' => ['required'],
                'micr' => ['required'],
                'account_type' => ['required'],
                'account_no' => ['required','numeric'],
            ];
        }elseif($this->step == 6)
        {
            if($this->department_id == 1)
            {
                if($this->profession_id == 1)
                {
                    return [
                        'nism_va_no' => ['required'],
                        'nism_va_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_va_validity' => ['bail','nullable','required','after:today'],
                    ];
                }elseif($this->profession_id  == 2)
                {
                    return [
                        'ria_certificate_type' => ['required'],
                        'ria_type_nism' => ['numeric'],
                        'ria_type_cfp' => ['numeric'],
                        'ria_type_cwm' => ['numeric'],
                        'nism_xa_no' => ['required_if:ria_type_nism,1'],
                        'nism_xa_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_xa_validity' => ['bail','nullable','required_if:ria_type_nism,1','after:today'],
                        'nism_xb_no' => ['required_if:ria_type_nism,1'],
                        'nism_xb_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_xb_validity' => ['bail','nullable','required_if:ria_type_nism,1','after:today'],
                        'cfp_no' => ['required_if:ria_type_cfp,1'],
                        'cfp_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'cfp_validity' => ['bail','nullable','required_if:ria_type_cfp,1','after:today'],
                        'cwm_no' => ['required_if:ria_type_cwm,1'],
                        'cwm_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'cwm_validity' => ['bail','nullable','required_if:ria_type_cwm,1','after:today'],
                    ];

                }else{
                    return [
                        'nism_va_no' => ['required_if:mfd_ria_type_mfd,1'],
                        'nism_va_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_va_validity' => ['bail','nullable','required_if:mfd_ria_type_mfd,1','after:today'],
                        'ria_certificate_type' => ['required_if:mfd_ria_type_ria,1'],
                        'ria_type_nism' => ['numeric'],
                        'ria_type_cfp' => ['numeric'],
                        'ria_type_cwm' => ['numeric'],
                        'nism_xa_no' => ['required_if:ria_type_nism,1'],
                        'nism_xa_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_xa_validity' => ['bail','nullable','required_if:ria_type_nism,1','after:today'],
                        'nism_xb_no' => ['required_if:ria_type_nism,1'],
                        'nism_xb_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'nism_xb_validity' => ['bail','nullable','required_if:ria_type_nism,1','after:today'],
                        'cfp_no' => ['required_if:ria_type_cfp,1'],
                        'cfp_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'cfp_validity' => ['bail','nullable','required_if:ria_type_cfp,1','after:today'],
                        'cwm_no' => ['required_if:ria_type_cwm,1'],
                        'cwm_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                        'cwm_validity' => ['bail','nullable','required_if:ria_type_cwm,1','after:today'],
                    ];
                }
            }else{
                return [
                    'ca_no' => ['required_if:ca_type,1'],
                    'ca_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                    'ca_validity' => ['bail','nullable','after:today'],
                    'cs_no' => ['required_if:cs_type,1'],
                    'cs_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                    'cs_validity' => ['bail','nullable','after:today'],
                    'course_name' => ['required_if:ot_type,1'],
                    'course_upload' => ['bail','nullable','mimes:jpeg,png,jpg,bmp,pdf,gif','max:500'],
                    'course_no' => ['required_if:ot_type,1'],
                    'course_validity' => ['bail','nullable','after:today'],
                ];
            }

        }elseif($this->step == 7){
            //dd($this->step);
            return [
            'bse_upload' => ['required'],
            ];
        }
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        //Step1
        if($this->step == 1)
        {
            return [

                'associate_id.required' => 'Select Associate name',
                'name.required' => 'Enter Employee name',
                'department_id.required' => 'Select Department',
                'subdepartment_id.required' => 'Select Sub Department tag',
                'designation_id.required' => 'Select Grade',
                'supervisor_id.required' => 'Select Supervisor Name'
            ];
        }
        //Step2
        elseif($this->step == 2)
        {
            return [
                'mobile.required' => 'Enter Mobile No',
                'mobile.numeric' => 'Enter only numeric value',
                'mobile.regex' => 'Enter valid mobile no',
                'email.required' => 'Enter Email Address',
                'email.email' => 'Enter Valid email address',
                'telephone.numeric' => 'Enter only numeric value',
                'blood_group.required' => 'Select Blood Group',
                'contact_name1.required' => 'Enter Name',
                'contact_mobile1.required' => 'Enter Mobile NO',
                'contact_mobile1.numeric' => 'Enter only numeric value',
                'contact_mobile1.regex' => 'Enter valid mobile no',
                'contact_email1.required' => 'Enter Email Address',
                'contact_email1.email' => 'Enter Valid email address',
                'contact_name2.required' => 'Enter Name',
                'contact_mobile2.required' => 'Enter Mobile No',
                'contact_mobile2.numeric' => 'Enter only numeric value',
                'contact_mobile2.regex' => 'Enter valid mobile no',
                'contact_email2.required' => 'Enter Email Address',
                'contact_email2.email' => 'Enter Valid email address',
            ];
        }
        //step3
        elseif($this->step == 3)
        {
            return [
                'pan_no.required' => 'Enter Pan No',
                'pan_no.regex' => 'Enter valid Pan Card No',
                'pan_upload.required_if' => 'Upload Pan Card',
                'pan_upload.mimes' => 'Not a Proper Format',
                'pan_upload.max' => 'Max Size 5kb',
                'aadhar_no.required' => 'Enter Aadhar No',
                'aadhar_no.numeric' => 'Enter only numeric value',
                'aadhar_no.regex' => 'Enter Valid Aadhar Card No',
                'aadhar_upload.required_if' => 'Upload Aadhar Card',
                'aadhar_upload.mimes' => 'Not a Proper Format',
                'aadhar_upload.max' => 'Max Size 5kb',
                'birth_date.required' => 'Enter birth date',
                'birth_date.before' => 'Date must be less than current date',
                'anniversary_date.before' => 'Date must be less than current date',
                'photo_upload.required_if' => 'Upload Photo',
                'photo_upload.mimes' => 'Not a Proper Format',
                'photo_upload.max' => 'Max Size 5kb',
            ];
        }
        //step4
        elseif($this->step == 4){
            return [
                'c_address1.required' => 'Enter Address Details',
                'c_address2.required' => 'Enter Address Details',
                'c_city.required' => 'Enter City Name',
                'c_state.required' => 'Select State',
                'c_country.required' => 'Select Country',
                'c_pincode.required' => 'Enter Pincode',
                'c_address_upload.required_if' => 'Upload Address',
                'c_address_upload.mimes' => 'Not a Proper Format',
                'c_address_upload.max' => 'Max Size 5kb',
                'p_address1.required_if' => 'Enter Address Details',
                'p_address2.required_if' => 'Enter Address Details',
                'p_city.required_if' => 'Enter City Name',
                'p_state.required_if' => 'Select State',
                'p_country.required_if' => 'Select Country',
                'p_pincode.required_if' => 'Enter Pincode',
                'p_address_upload.required' => 'Upload Address',
                'p_address_upload.mimes' => 'Not a Proper Format',
                'p_address_upload.max' => 'Max Size 5kb',
            ];
        }
        //step5
        elseif($this->step == 5)
        {
            return [
                'ifsc_no.required' => 'Enter IFSC code',
                'cheque_upload.required_if' => 'Upload Cheque',
                'cheque_upload.mimes' => 'Not a Proper Format',
                'cheque_upload.max' => 'Max Size 5kb',
                'bank_name.required' => 'Enter Bank name',
                'branch_name.required' => 'Enter Bank Branch Name',
                'micr.required' => 'Enter MICR',
                'account_type.required' => 'Select Account Type',
                'account_no.required' => 'Enter Account No',
                'account_no.numeric' => 'Account No must be in Numeric',
            ];
        }elseif($this->step == 7){
            return [
            'bse_upload.required' => 'Please upload a file to BSE',
            ];
        }else{
            return [
                'nism_va_no.required' => 'Enter Certificate No',
                'nism_va_no.required_if' =>'Enter Certificate No',
                'nism_va_upload.required' => 'Upload Certificate',
                'nism_va_upload.mimes' => 'Not a Proper Format',
                'nism_va_upload.max' => 'Max Size 5kb',
                'nism_va_validity.required_if' => 'Enter Certificate Validity',
                'nism_va_validity.required' => 'Enter Certificate Validity',
                'nism_va_validity.after' => 'Date must be greater then today',
                'ria_certificate_type.required' => 'Select RIA Type',
                'ria_certificate_type.required_if' => 'Select RIA Type',
                'nism_xa_no.required_if' => 'Enter Certificate No',
                'nism_xa_upload.required' => 'Upload Certificate',
                'nism_xa_upload.mimes' => 'Not a Proper Format',
                'nism_xa_upload.max' => 'Max Size 5kb',
                'nism_xa_validity.required_if' => 'Enter Certificate Validity',
                'nism_xa_validity.after' => 'Date must be greater then today',
                'nism_xb_no.required_if' => 'Enter Certificate No',
                'nism_xb_upload.required' => 'Upload Certificate',
                'nism_xb_upload.mimes' => 'Not a Proper Format',
                'nism_xb_upload.max' => 'Max Size 5kb',
                'nism_xb_validity.required_if' => 'Enter Certificate Validity',
                'nism_xb_validity.after' => 'Date must be greater then today',
                'cfp_no.required_if' => 'Enter Certificate No',
                'cfp_upload.required' => 'Upload Certificate',
                'cfp_upload.mimes' => 'Not a Proper Format',
                'cfp_upload.max' => 'Max Size 5kb',
                'cfp_validity.required_if' => 'Enter Certificate Validity',
                'cfp_validity.after' => 'Date must be greater then today',
                'cwm_no.required_if' => 'Enter Certificate No',
                'cwm_upload.required' => 'Upload Certificate',
                'cwm_upload.mimes' => 'Not a Proper Format',
                'cwm_upload.max' => 'Max Size 5kb',
                'cwm_validity.required_if' => 'Enter Certificate Validity',
                'cwm_validity.after' => 'Date must be greater then today',
                'ca_cs_type.required' => 'Select Course',
                'ca_no.required_if' => 'Enter Certificate No',
                'ca_upload.required' => 'Upload Certificate',
                'ca_upload.mimes' => 'Not a Proper Format',
                'ca_upload.max' => 'Max Size 5kb',
                'ca_validity.after' => 'Date must be greater then today',
                'cs_no.required_if' => 'Enter Certificate No',
                'cs_upload.required' => 'Upload Certificate',
                'cs_upload.mimes' => 'Not a Proper Format',
                'cs_upload.max' => 'Max Size 5kb',
                'cs_validity.after' => 'Date must be greater then today',
                'course_name.required_if' => 'Enter Course Name',
                'course_upload.required' => 'Upload Certificate',
                'course_upload.mimes' => 'Not a Proper Format',
                'course_upload.max' => 'Max Size 5kb',
                'course_no.required_if' => 'Enter Certificate No',
                'course_validity.after' => 'Date must be greater then today',
            ];
        }


    }


    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes('p_address_upload', 'required', function($input) {
            return $input->p_address1 != '' && $input->employee_edit == 0  && $input->step == 4;
        });
        $validator->sometimes('nism_va_upload', 'required', function($input) {
            return $input->nism_va_no != null && $input->employee_edit == 0  && $input->step == 6;
        });
        $validator->sometimes('nism_xa_upload', 'required', function($input) {
            return $input->nism_xa_no != null && $input->employee_edit == 0  && $input->step == 6;
        });
        $validator->sometimes('nism_xb_upload', 'required', function($input) {
            return $input->nism_xb_no != null && $input->employee_edit == 0  && $input->step == 6;
        });
        $validator->sometimes('cfp_upload', 'required', function($input) {
            return $input->cfp_no != null && $input->employee_edit == 0  && $input->step == 6;
        });
        $validator->sometimes('cwm_upload', 'required', function($input) {
            return $input->cwm_no != null && $input->employee_edit == 0  && $input->step == 6;
        });
        $validator->sometimes('ca_upload', 'required', function($input) {
            return $input->employee_edit == 0  && $input->step == 6 && $input->ca_type == 1;
        });
        $validator->sometimes('cs_upload', 'required', function($input) {
            return $input->employee_edit == 0  && $input->step == 6 && $input->cs_type == 1;
        });
        $validator->sometimes('course_upload', 'required', function($input) {
            return $input->employee_edit == 0  && $input->step == 6 && $input->ot_type == 1;
        });
        return $validator;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         //dd($validator->errors());
    //         // if ($this->somethingElseIsInvalid()) {

    //         //     $validator->errors()->add('field', 'Something is wrong with this field!');
    //         // }
    //     });
    // }
}
