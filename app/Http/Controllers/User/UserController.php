<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Models\Associate\Associate;
use App\Models\Associate\AssociateAuthorise;
use App\Models\Associate\AssociateGuradian;
use App\Models\Associate\AssociateNominee;
use App\Models\Associate\Employee;
use App\Models\associate\EmployeeContact;
use App\Models\User;
use App\Services\MyServices;
use Illuminate\Http\Request;

class UserController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api')->only(['show','logout','user','checkAuthUserMobile','checkAuthUserEmail','checkAuthUserPanNo']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $this->showAll($users);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $request->user()->role = $request->user()->roles->pluck('slug');
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->showMessage('Logout successfully');
    }

    /**
     * Check the User Name is available or not in database before forgot password
     *
     * @return bool
     */
    public function checkUserName(Request $request)
    {
        $username = MyServices::getEncryptedString(strtoupper($request->username));
        $user = User::where('username', $username)->first();
        return $this->showMessage($user ? true : false);
    }

    /**
     * Check the User Mobile is available or not in database
     *
     * @return bool
     */
    public function checkAuthUserMobile(Request $request)
    {
        // $mobile = false;

        // if($request->mobile)
        // {
        //     $usermobile = MyServices::getEncryptedString($request->mobile);

        //     if(!empty($request->associate_id))
        //     {
        //         $associate = Associate::findOrFail($request->associate_id);

        //         $mobile = User::where('mobile', $usermobile)->where('id', '!=' ,$associate->user_id)->first();
        //     }else{
        //         $mobile = User::where('mobile', $usermobile)->first();
        //     }
        // }
        // return $this->showMessage($mobile ? true : false);

        $usermobile = MyServices::getEncryptedString($request->mobile);
        $pan_no = '';
        if($request->pan_no)
        {
            $pan_no = MyServices::getEncryptedString(strtoupper($request->pan_no));
        }

        $mobile = false;
        if($request->mobile)
        {
            if($request->user()->hasPanNo($pan_no) && !empty($pan_no))
            {
                $mobile = User::where('mobile', $usermobile)->where('username', '!=' ,$pan_no)->first();
            }else{
                $mobile = User::where('mobile', $usermobile)->first();
            }
        }
        if($mobile)
        {
            return $this->showMessage($mobile ? true : false);
        }

        $associate = Associate::Where('pan_no',$pan_no)->where('id', $request->associate_id)->first();
        if($request->associate_id && $associate)
        {
            $mobile = AssociateAuthorise::where('mobile', $usermobile)->where('associate_id', '!=', $request->associate_id)->first();
            if(!$mobile)
            {
                $mobile = AssociateNominee::where('nominee_mobile', $usermobile)->where('associate_id', '!=', $request->associate_id)->first();
                if(!$mobile)
                {
                    $nominee_id = $associate->associateNominee ? $associate->associateNominee->id : null;
                    if($nominee_id)
                    $mobile = AssociateGuradian::where('guardian_mobile', $usermobile)->where('associate_nominee_id', $nominee_id)->first();
                }
            }
        }else{
            $mobile = AssociateAuthorise::where('mobile', $usermobile)->first();
            if(!$mobile)
            {
                $mobile = AssociateNominee::where('nominee_mobile', $usermobile)->first();
                if(!$mobile)
                {
                    $mobile = AssociateGuradian::where('guardian_mobile', $usermobile)->first();
                }
            }
        }

        if($mobile)
        {
            return $this->showMessage($mobile ? true : false);
        }

        $employee = Employee::Where('pan_no',$pan_no)->where('id', $request->employee_id)->first();
        if($request->employee_id && $employee)
        {
            $mobile = EmployeeContact::where('mobile', $usermobile)->where('employee_id', '!=', $request->employee_id)->first();
        }else{
            $mobile = EmployeeContact::where('mobile', $usermobile)->first();

        }

        if($mobile)
        {
            return $this->showMessage($mobile ? true : false);
        }


        return $this->showMessage($mobile ? true : false);
    }

    /**
     * Check the User Email is available or not in database
     *
     * @return bool
     */
    public function checkAuthUserEmail(Request $request)
    {
        $useremail = MyServices::getEncryptedString(strtolower($request->email));

        $pan_no = '';
        if($request->pan_no)
        {
            $pan_no = MyServices::getEncryptedString(strtoupper($request->pan_no));
        }
        $email = false;
        if($request->email)
        {


            if($request->user()->hasPanNo($pan_no) && !empty($pan_no))
            {
                $email = User::where('email', $useremail)->where('username', '!=' ,$pan_no)->first();
            }else{
                $email = User::where('email', $useremail)->first();
            }
        }

        if($email)
        {
            return $this->showMessage($email ? true : false);
        }

        $associate = Associate::Where('pan_no',$pan_no)->where('id', $request->associate_id)->first();
        if($request->associate_id && $associate)
        {
            $email = AssociateAuthorise::where('email', $useremail)->where('associate_id', '!=', $request->associate_id)->first();
            if(!$email)
            {
                $email = AssociateNominee::where('nominee_email', $useremail)->where('associate_id', '!=', $request->associate_id)->first();
                if(!$email)
                {
                    $nominee_id = $associate->associateNominee ? $associate->associateNominee->id : null;
                    if($nominee_id)
                    $email = AssociateGuradian::where('guardian_email', $useremail)->where('associate_nominee_id', $nominee_id)->first();
                }
            }
        }else{
            $email = AssociateAuthorise::where('email', $useremail)->first();
            if(!$email)
            {
                $email = AssociateNominee::where('nominee_email', $useremail)->first();
                if(!$email)
                {
                    $email = AssociateGuradian::where('guardian_email', $useremail)->first();
                }
            }
        }

        if($email)
        {
            return $this->showMessage($email ? true : false);
        }

        $employee = Employee::Where('pan_no',$pan_no)->where('id', $request->employee_id)->first();
        if($request->employee_id && $employee)
        {
            $email = EmployeeContact::where('email', $useremail)->where('employee_id', '!=', $request->employee_id)->first();
        }else{
            $email = EmployeeContact::where('email', $useremail)->first();

        }

        if($email)
        {
            return $this->showMessage($email ? true : false);
        }


        return $this->showMessage($email ? true : false);
    }

    public function checkAuthUserPanNo(Request $request)
    {
        $pan_no = MyServices::getEncryptedString(strtoupper($request->pan_no));
        $panno = User::where('username', $pan_no)->first();
        if($panno)
        {
            if(!empty($request->associate_id))
            {
                $panno = Associate::Where('pan_no',$pan_no)->where('id', '!=',$request->associate_id)->first();
            }
            if(!empty($request->employee_id))
            {
                $panno = Employee::Where('pan_no',$pan_no)->where('id', '!=',$request->employee_id)->first();
            }
        }
        return $this->showMessage($panno ? true : false);

    }
}
