<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\ApiController;
use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use App\Models\Master\Country;
use App\Models\Master\Department;
use App\Models\Master\Designation;
use App\Models\Master\State;
use App\Models\User;
use App\Traits\ManageUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExternalEmployeeController extends ApiController
{
    use ManageUser;
    public function __construct()
    {
        $this->middleware('client.credentials');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $employee = Employee::find($request->employee_id);

        if($request->status == 5 || $request->status == 6 || $request->status == 7 || $request->status == 8)
        {
            if($request->userstatus == 0)
            {
                $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->EmployeeApprovedSelfDetail($request));
                if($request->department_id == 1)
                {
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->UserBSEUploadPending($request));
                }else{
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->UserActiveStatus($request));
                }
            }

            if($request->userstatus == 1)
            {
                $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->EmployeeRejectedSelfDetail($request));
            }
        }
        $employee->status = $employee->employeeMakerChecker->status_id;
        return $this->showMessage($employee);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = Employee::find($id);
        if($employee)
        {
            if($user = $employee->user)
            {
                $employee->mobile = $user->mobile;
                $employee->email = $user->email;
            }
            $employee->associate_name = Associate::firstWhere('id', $employee->associate_id)->entity_name;
            $employee->department_name = Department::firstWhere('id', $employee->department_id)->name;
            $employee->subdepartment_name = Department::firstWhere('id', $employee->subdepartment_id)->name;
            $employee->designation_name = Designation::firstWhere('id', $employee->designation_id)->name;
            $employee->supervisor_name = User::firstWhere('id', $employee->supervisor_id)->name;
            if($employee->birth_date)
            $employee->birth_date = Carbon::parse($employee->birth_date)->format('d-m-Y');

            if($employee->anniversary_date)
            $employee->anniversary_date = Carbon::parse($employee->anniversary_date)->format('d-m-Y');

            if($contacts = $employee->employeeContant)
            {
                foreach($contacts as $contact)
                {
                    if(isset($contact->cno))
                    {
                        $name = "contact_name{$contact->cno}";
                        $mobile = "contact_mobile{$contact->cno}";
                        $email  = "contact_email{$contact->cno}";
                        $employee->$name = $contact->name;
                        $employee->$mobile = $contact->mobile;
                        $employee->$email = $contact->email;
                    }
                }
            }
            if($employee->files)
            {
                foreach($employee->files as $file)
                {
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $employee->$name = $value;
                }
            }
            if($addresses = $employee->addresses)
            {
                foreach($addresses as $address)
                {
                    $file = $address->file;
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $address->$name = $value;

                    if($address->addresstype_id == 1)
                    {
                        $employee->c_address1 = $address->address1;
                        $employee->c_address2 = $address->address2;
                        $employee->c_address3 = $address->address3;
                        $employee->c_city = $address->city;
                        $employee->c_state = $address->state;
                        $employee->c_statename = State::firstWhere('id', $address->state)->name;
                        $employee->c_country = $address->country;
                        $employee->c_countryname = Country::firstWhere('id', $address->country)->name;
                        $employee->c_pincode = $address->pincode;
                        $employee->c_address_upload = $value;
                    }
                    if($address->addresstype_id == 4)
                    {
                        $employee->p_address1 = $address->address1;
                        $employee->p_address2 = $address->address2;
                        $employee->p_address3 = $address->address3;
                        $employee->p_city = $address->city;
                        $employee->p_state = $address->state;
                        $employee->p_statename = State::firstWhere('id', $address->state)->name;
                        $employee->p_country = $address->country;
                        $employee->p_countryname = Country::firstWhere('id', $address->country)->name;
                        $employee->p_pincode = $address->pincode;
                        $employee->p_address_upload = $value;
                    }
                }
            }
            if($bank = $employee->bank)
            {
                $file = $bank->file;
                $name = $file->fieldname;
                $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                $bank->$name = $value;

                $employee->ifsc_no = $bank->ifsc_no;
                $employee->bank_name = $bank->bank_name;
                $employee->branch_name = $bank->branch_name;
                $employee->micr = $bank->micr;
                $employee->account_type = $bank->account_type;
                $employee->account_no = $bank->account_no;
                $employee->$name = $value;
            }
            if($certificate = $employee->employeeCertificate){
                if($certificate->nism_va_validity)
                $certificate->nism_va_validity = Carbon::parse($certificate->nism_va_validity)->format('d-m-Y');

                if($certificate->nism_xa_validity)
                $certificate->nism_xa_validity = Carbon::parse($certificate->nism_xa_validity)->format('d-m-Y');

                if($certificate->nism_xb_validity)
                $certificate->nism_xb_validity = Carbon::parse($certificate->nism_xb_validity)->format('d-m-Y');

                if($certificate->cfp_validity)
                $certificate->cfp_validity = Carbon::parse($certificate->cfp_validity)->format('d-m-Y');

                if($certificate->cwm_validity)
                $certificate->cwm_validity = Carbon::parse($certificate->cwm_validity)->format('d-m-Y');

                if($certificate->ca_validity)
                $certificate->ca_validity = Carbon::parse($certificate->ca_validity)->format('d-m-Y');

                if($certificate->cs_validity)
                $certificate->cs_validity = Carbon::parse($certificate->cs_validity)->format('d-m-Y');

                if($certificate->course_validity)
                $certificate->course_validity = Carbon::parse($certificate->course_validity)->format('d-m-Y');

                if($employee->employeeCertificate->files)
                {
                    foreach($certificate->files as $file)
                    {
                        $name = $file->fieldname;
                        $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                        $certificate->$name = $value;
                    }
                }
            }

            if($makerChecker = $employee->employeeMakerChecker)
            {
                $employee->status = $makerChecker->status_id;
                $employee->makerchecker = $makerChecker;
                if($makerCheckerlog = $makerChecker->makercheckerlogs)
                {
                    $employee->makercheckerlog = $makerCheckerlog;
                }
            }

        }
        return $this->showOne($employee);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
