<?php

namespace App\Http\Controllers\Associate;

use App\Http\Controllers\ApiController;
use App\Http\Requests\EmployeeRequest;
use App\Models\Address;
use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use App\Models\Bank;
use App\Models\Master\Country;
use App\Models\Master\Department;
use App\Models\Master\Designation;
use App\Models\Master\State;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\MyServices;
use App\Services\Security;
use App\Traits\ManageAddress;
use App\Traits\ManageBank;
use App\Traits\ManageEmployee;
use App\Traits\ManageFile;
use App\Traits\ManageUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends ApiController
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
    public function index(Request $request)
    {
        if(!empty($request->associate_id))
        {
            $associate = Associate::firstWhere('id', $request->associate_id);
            $employees = $associate->employees;
        }else{
            $employees = Employee::all();
        }

        foreach($employees as $employee)
        {
            $user = $employee->user;
            $employee->associate_name = Associate::firstWhere('id', $employee->associate_id)->entity_name;
            $address = $employee->addresses->firstWhere('addresstype_id',1);
            // $associate->entity_code = Entitytype::firstwhere('id', $associate->entitytype_id)->name;
            $employee->mobile = $user->mobile;
            $employee->email = $user->email;
            $employee->user_active = $user->is_active;
            $employee->user_first = $user->is_first;
            $employee->user_inhouse = $user->in_house;
            $employee->last_login_at = $user->last_login_at;
            $employee->last_login_ip = $user->last_login_ip;
            $employee->department_name = Department::firstWhere('id', $employee->department_id)->name;
            $employee->subdepartment_name = Department::firstWhere('id', $employee->subdepartment_id)->name;
            $employee->designation_name = Designation::firstWhere('id', $employee->designation_id)->name;
            if($employee->employeeMakerChecker)
            {
                $employee->status = $employee->employeeMakerChecker->status_id;
                $employee->status_code = $employee->employeeMakerChecker->admin_comment;
            }

            if($address)
            {
                $employee->location = $address->city;
            }else{
                $employee->location = '';
            }
        }
        return $this->showAll($employees);
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
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
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

    /**
     *  Download the specific resource
     */
    public function download($id)
    {
        //        1   Terminal ID
        //        2   Login id
        //        3   branch id
        //        4   password
        //        5   Name
        //        6   Subbroker code
        //        7   Sub broker ARN code
        //        8   Subbrooker EUIN
        //        9   Address1
        //        10  Address2
        //        11  Address3
        //        12  City
        //        13  State
        //        14  Pin
        //        15  Country
        //        16  Phone
        //        17  mobile
        //        18  fax
        //        19  email
        //        20  Access level
        $data = "{1}|{2}|{3}|{4}|{5}|{6}|{7}|{8}|{9}|{10}|{11}|{12}|{13}|{14}|{15}|{16}|{17}|{18}|{19}|{20}";
        //dd($data);
        $employee = Employee::find($id);
        $associate = Associate::find($employee->associate_id);
        $employee->mobile = $employee->user->mobile;
        $employee->email = $employee->user->email;

        if($alicence = $associate->associateLicence)
        {
            $employee->arn = $alicence->arn_rgn_no;
            //$employee->euin = $licence->euin_no;
        }

        if($elicence = $employee->employeeLicence)
        {
            $employee->euin = $elicence->euin_no;
        }
        //dd($employee->euin);
        if($address = $employee->addresses->firstWhere('is_default',1))
        {
            $employee->address1 = $address->address1;
            $employee->address2 = $address->address2;
            $employee->address3 = $address->address3;
            $employee->city = $address->city;
            $employee->state = State::firstWhere('id', $address->state)->code;
            $employee->country = Country::firstWhere('id', $address->country)->name;
            $employee->pincode = $address->pincode;
        }
        //dd(Security::decryptData($associate->bse_password));
        //$associate->user;
        if ($employee != null)
        {
            $data = str_replace('{1}', 'DEALER', $data);
            $data = str_replace('{2}', $associate->associate_code, $data);
            $data = str_replace('{3}', 'CORPBRANCH', $data);
            $data = str_replace('{4}', Security::decryptData($associate->bse_password), $data);
            $data = str_replace('{5}', $employee->name, $data);
            $data = str_replace('{6}', $associate->associate_code, $data);
            $data = str_replace('{7}', $employee->arn, $data);
            $data = str_replace('{8}', $employee->euin, $data);
            $data = str_replace('{9}', str_replace( ',', ' ', $employee->address1 ), $data);
            $data = str_replace('{10}', str_replace( ',', ' ', $employee->address2 ), $data);
            $data = str_replace('{11}', str_replace( ',', ' ', $employee->address3 ), $data);
            $data = str_replace('{12}', $employee->city, $data);
            $data = str_replace('{13}', $employee->state, $data);
            $data = str_replace('{14}', $employee->pincode, $data);
            $data = str_replace('{15}', $employee->country, $data);
            $data = str_replace('{16}', $employee->telephone, $data);
            $data = str_replace('{17}', $employee->mobile, $data);
            $data = str_replace('{18}', '', $data);
            $data = str_replace('{19}', $employee->email, $data);
            $data = str_replace('{20}', 'F', $data);
        }
        //dd($data);
        $code = $associate->associate_code . '_' . $employee->id;
        if (Storage::exists('/employeedocuments/' . $id . '/' . $code . '.csv'))
        {
            Storage::delete('/employeedocuments/' . $id . '/' . $code . '.csv');
        }

        Storage::put('/employeedocuments/' . $id . '/' . $code . '.csv', $data);

        $url = Storage::url('/employeedocuments/' . $id . '/' . $code . '.csv');
        return $this->showMessage($url);
    }

    public function getLogs($id)
    {
        $employee = Employee::find($id);
        $employee->logs = $employee->employeeMakerChecker->makercheckerlogs;
        return $this->showOne($employee);
    }
}
