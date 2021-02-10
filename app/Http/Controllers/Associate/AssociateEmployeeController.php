<?php

namespace App\Http\Controllers\Associate;

use App\Http\Controllers\ApiController;
use App\Http\Requests\EmployeeRequest;
use App\Models\Address;
use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use App\Models\Bank;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\MyServices;
use App\Traits\ManageAddress;
use App\Traits\ManageBank;
use App\Traits\ManageEmployee;
use App\Traits\ManageFile;
use App\Traits\ManageUser;
use Carbon\Carbon;

class AssociateEmployeeController extends ApiController
{
    use ManageUser, ManageEmployee, ManageAddress, ManageBank, ManageFile;

    protected $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $associate = Associate::find($id);
        if($associate)
        {
            $employees = $associate->employees()->where('department_id', 1)->active()->get();
            return $this->showAll($employees);
        }else{
            return $this->showMessage($associate);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Associate $associate, Request $request)
    {
        $employees = $associate->employees()->where('designation_id', '<', $request->designation_id)->get()->pluck('user_id');
        $associate_user = [];
        $associate_user[] = $associate->user_id;
        //dd($associate_user);
        $users = Role::where('slug', 'superadmin')->first()->users()->get()->pluck('id');
        //dd($users);
        $data = array_merge($employees->toArray(),$associate_user,$users->toArray());
        //dd($data);
        $user = User::whereIn('id', $data)->active()->get();

        return $this->showAll($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        //return $this->showMessage($request->all());
        if($request->has('pan_no'))
        {
            //Create Or Update User
            $user = $this->user->updateOrCreate(['username' => MyServices::getEncryptedString(strtoupper($request->pan_no))], $this->createUser($request));
            //Assign Role to User
            if(!$user->hasRole([Employee::EMPLOYEE_ROLE]))
            {
                $user->roles()->attach([$this->getRoleID(Employee::EMPLOYEE_ROLE)]);
            }
            // $authuser = $request->user();

            // if($authuser->in_house == true)
            // {
            //     $associate = Associate::where('id', $request->associate_id)->first();
            // }else{
            //     $associate = $authuser->associate;
            // }
            //return $this->showMessage($this->createEmployee($request));

            //$employee = Employee::create($this->createEmployee($request));

            $employee = $user->employee()->updateOrCreate(['pan_no' => MyServices::getEncryptedString(strtoupper($request->pan_no))], $this->createEmployee($request));
            if($request->employee_edit == 1 && $request->step_edit == 1)
            {
                if(!empty($request->contact_name1) && !empty($request->contact_mobile1) && !empty($request->contact_email1))
                {
                    $employee->employeeContant()->updateOrCreate(['employee_id' => $employee->id,'cno' => Employee::FIRST_CONTACT],$this->createEmployeeContact($request,Employee::FIRST_CONTACT));
                }
                if(!empty($request->contact_name2) && !empty($request->contact_mobile2) && !empty($request->contact_email2))
                {
                    $employee->employeeContant()->updateOrCreate(['employee_id' => $employee->id,'cno' => Employee::SECOND_CONTACT],$this->createEmployeeContact($request,Employee::SECOND_CONTACT));
                }
            }
            if($request->step == 3)
            {
                if(!empty($request->contact_name1) && !empty($request->contact_mobile1) && !empty($request->contact_email1))
                {
                    $employee->employeeContant()->updateOrCreate(['employee_id' => $employee->id,'cno' => Employee::FIRST_CONTACT],$this->createEmployeeContact($request,Employee::FIRST_CONTACT));
                }
                if(!empty($request->contact_name2) && !empty($request->contact_mobile2) && !empty($request->contact_email2))
                {
                    $employee->employeeContant()->updateOrCreate(['employee_id' => $employee->id,'cno' => Employee::SECOND_CONTACT],$this->createEmployeeContact($request,Employee::SECOND_CONTACT));
                }
                if($request->hasFile(Employee::EMPLOYEE_PHOTO))
                {
                    $employee->files()->updateOrCreate(['fileable_id' => $employee->id,'fieldname' => Employee::EMPLOYEE_PHOTO, 'fileable_type' => Employee::EMPLOYEE_MODEL],$this->uploadSingleFile($request, Employee::EMPLOYEE_PHOTO, $employee->id));
                }
                if($request->hasFile(Employee::EMPLOYEE_PAN))
                {
                    $employee->files()->updateOrCreate(['fileable_id' => $employee->id,'fieldname' => Employee::EMPLOYEE_PAN, 'fileable_type' => Employee::EMPLOYEE_MODEL],$this->uploadSingleFile($request, Employee::EMPLOYEE_PAN, $employee->id));
                }
                if($request->hasFile(Employee::EMPLOYEE_AADHAR))
                {
                    $employee->files()->updateOrCreate(['fileable_id' => $employee->id,'fieldname' => Employee::EMPLOYEE_AADHAR, 'fileable_type' => Employee::EMPLOYEE_MODEL],$this->uploadSingleFile($request, Employee::EMPLOYEE_AADHAR, $employee->id));
                }
            }
            if($request->step == 4)
            {
                //Save Correspondence Address
                if(!empty($request->c_address1))
                {
                    //return $this->showMessage($this->createCorrespondenceAddress($request));

                    $address = $employee->addresses()->updateOrCreate(['addressable_id' => $employee->id,'addresstype_id' => Address::CORRESPONDENCE_ID, 'addressable_type' => Employee::EMPLOYEE_MODEL],$this->createCorrespondenceAddress($request));

                    if($request->hasFile(Address::CORRESPONDENCE_ADDRESS_UPLOAD))
                    {
                        $address->file()->updateOrCreate(['fileable_id' => $address->id,'fieldname' => Address::CORRESPONDENCE_ADDRESS_UPLOAD],$this->uploadSingleFile($request, Address::CORRESPONDENCE_ADDRESS_UPLOAD, $employee->id, $address->id));
                    }
                }
                //Save Parmanent Address
                if(!empty($request->p_address1))
                {
                    $address = $employee->addresses()->updateOrCreate(['addressable_id' => $employee->id,'addresstype_id' => Address::PARMANENT_ID, 'addressable_type' => Employee::EMPLOYEE_MODEL],$this->createParmanentAddress($request));

                    if($request->hasFile(Address::PARMANENT_ADDRESS_UPLOAD))
                    {
                        $address->file()->updateOrCreate(['fileable_id' => $address->id,'fieldname' => Address::PARMANENT_ADDRESS_UPLOAD],$this->uploadSingleFile($request, Address::PARMANENT_ADDRESS_UPLOAD, $employee->id, $address->id));
                    }
                }
            }
            if($request->step == 5)
            {
                $bank = $employee->bank()->updateOrCreate(['bankable_id' => $employee->id,'is_default' => Bank::BANK_DEFAULT],$this->createBank($request));

                if($request->hasFile(Bank::BANK_UPLOAD))
                {
                    $bank->file()->updateOrCreate(['fileable_id' => $bank->id,'fieldname' => Bank::BANK_UPLOAD],$this->uploadSingleFile($request, Bank::BANK_UPLOAD, $employee->id, $bank->id));
                }
            }
            if($request->step == 6)
            {

                $employeeCertificate = $employee->employeeCertificate()->updateOrCreate(['certificateable_id' => $employee->id,'certificateable_type' => Employee::EMPLOYEE_MODEL],$this->createEmployeeCertificate($request));

                if($request->profession_id == 1)
                {
                    $employeeLicence = $employee->employeeLicence()->updateOrCreate(['employee_id' => $employee->id],$this->createEmployeeLicence($request));
                    if($request->hasFile(Associate::EUIN_UPLOAD))
                    {
                        $employeeLicence->files()->updateOrCreate(['fileable_id' => $employeeLicence->id,'fieldname' => Employee::EUIN_UPLOAD],$this->uploadSingleFile($request, Employee::EUIN_UPLOAD, $employee->id, $employeeLicence->id));
                    }
                }
                if($request->hasFile(Employee::NISM_VA_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::NISM_VA_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::NISM_VA_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::NISM_XA_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::NISM_XA_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::NISM_XA_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::NISM_XB_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::NISM_XB_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::NISM_XB_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::CFP_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::CFP_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::CFP_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::CWM_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::CWM_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::CWM_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::CA_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::CA_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::CA_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::CS_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::CS_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::CS_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->hasFile(Employee::COURSE_UPLOAD))
                {
                    $employeeCertificate->files()->updateOrCreate(['fileable_id' => $employeeCertificate->id,'fieldname' => Employee::COURSE_UPLOAD, 'fileable_type' => Employee::CERFITICATE_FILE],$this->uploadSingleFile($request, Employee::COURSE_UPLOAD, $employee->id, $employeeCertificate->id));
                }
                if($request->status == 1 && $request->employee_edit == 1 && $request->step_edit == 1)
                {
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeChecker($request,$user));
                }
                //Suervisior Accept Case
                if($request->status == 2 && $request->userstatus == 0)
                {
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeApprovedStatus($request));
                }
                //Supervisior Reject Case
                if($request->status == 2 && $request->userstatus == 1)
                {
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeRejectedStatus($request));
                }
                //Rejected Status (Supervisior & Employee)
                if($request->status == 4 || $request->status == 7)
                {
                    $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeReChecker($request,$user));
                }
            }
            return $this->showOne($employee);
        }else{
            return $this->showMessage($request->all());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Associate $associate, Employee $employee)
    {
        if($employee)
        {
            if($user = $employee->user)
            {
                $employee->mobile = $user->mobile;
                $employee->email = $user->email;
            }
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
                    $value = '';
                    if($file = $address->file)
                    {
                        $name = $file->fieldname;
                        $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                        $address->$name = $value;
                    }

                    if($address->addresstype_id == 1)
                    {
                        $employee->c_address1 = $address->address1;
                        $employee->c_address2 = $address->address2;
                        $employee->c_address3 = $address->address3;
                        $employee->c_city = $address->city;
                        $employee->c_state = $address->state;
                        $employee->c_country = $address->country;
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
                        $employee->p_country = $address->country;
                        $employee->p_pincode = $address->pincode;
                        $employee->p_address_upload = $value;
                    }
                }
            }
            if($bank = $employee->bank)
            {
                $name = 'cheque_upload';
                $value = '';
                if($file = $bank->file)
                {
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $bank->$name = $value;
                }
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

            if($licence = $employee->employeeLicence){
                $employee->euin_name = $licence->euin_name;
                $employee->euin_no = $licence->euin_no;
                $employee->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
                if($certificate->nism_va_validity)
                $licence->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
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
