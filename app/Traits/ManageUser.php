<?php

namespace App\Traits;

use App\Models\Associate\Associate;
use App\Models\Master\Status;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\MyServices;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Facades\URL;

trait ManageUser
{

    private $userData = ['name','email', 'username' ,'mobile', 'pin', 'password', 'is_active', 'is_first',
    'in_house','activation_token'];
    private $updateUserData = ['name','email' ,'mobile'];

    private $associateCreate = ['title', 'description', 'link', 'is_read'];

    private $makerChecker = ['maker_id', 'checker_id', 'status_id', 'maker_comment', 'admin_comment', 'is_accept_by_checker', 'is_reject_by_checker', 'checker_reject_reason', 'is_accept_by_user', 'is_reject_by_user', 'user_reject_reason'];

    private $makerCheckerLog = ['user_id', 'status_id', 'user_comment'];

    protected function createUser($request)
    {
        $username = MyServices::getEncryptedString(strtoupper($request->pan_no));
        $user = User::where('username', $username)->first();

        if(!$user)
        {
            $data = request($this->userData);
            $password = 'kinntegra@123';
            /**
             *  Below if condition executive only for Associate Module
             */
            if(!empty($request->birth_incorp_date))
            {
                $month = Carbon::parse($request->birth_incorp_date)->format('m');
                $date = Carbon::parse($request->birth_incorp_date)->format('d');
                $password = strtoupper(substr($request->pan_no, 0, 5)).$date.$month;
            }
            /**
             *  Below if condition executive only for Employee Module
             */
            if(!empty($request->birth_date))
            {
                $month = Carbon::parse($request->birth_date)->format('m');
                $date = Carbon::parse($request->birth_date)->format('d');
                $password = strtoupper(substr($request->pan_no, 0, 5)).$date.$month;
            }
            $data['password'] = Hash::make($password);
            $data['is_active'] = User::INACTIVE_USER;
            $data['activation_token'] = User::generateActivationCode();
            $data['username'] = $request->pan_no;
            //Generate PIN
            if($request->pin)
            $data['pin'] = $request->pin;
            $data['pin'] = '123456';

        }else{
            $data = request($this->updateUserData);
        }

        return $data;
    }

    public function getRoleID($role)
    {
        return Role::where('name', $role)
            ->orWhere('slug', $role)
            ->first()->id;

    }

    /**
     * Create Notification for New Associate Creation
     * Done
     */
    public function NewAssociateCreation($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::NEW_ASSOCIATE;
        $data['description'] = 'creation of new Associate '.$request->entity_name.' is initiated.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create Notification for New Employee Creation
     * Done
     */
    public function NewEmployeeCreation($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::NEW_EMPLOYEE;
        $data['description'] = 'creation of new Employee '.$request->name.' is initiated.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Supervisior Verification for Associate
     * Done
     */
    public function SupervisiorAssociateVerification($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        $data['description'] = 'Verification of new Associate '.$request->entity_name.' is Pending.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Supervisior Verification for Employee
     * Done
     */
    public function SupervisiorEmployeeVerification($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        $data['description'] = 'Verification of new Employee '.$request->name.' is Pending.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Supervisior Re-Verification for Associate
     * Done
     */
    public function SupervisiorAssociateReVerification($request,$associate,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        $data['description'] = 'Verification of Associate '.$associate->entity_name.' is Pending.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Supervisior Re-Verification for Employee
     * Done
     */
    public function SupervisiorEmployeeReVerification($request,$employee,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        $data['description'] = 'Verification of Employee '.$employee->name.' is Pending.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create Notification for Associate Self Verification
     * Done
     */
    public function AssociateSelfNotification($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_USER_SUPERVISIOR_STATUS;
        $data['description'] = 'Details send to Associate '.$request->entity_name.' for self verification.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create Notification for Employee Self Verification
     * Done
     */
    public function EmployeeSelfNotification($request,$link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_USER_SUPERVISIOR_STATUS;
        $data['description'] = 'Details send to Associate '.$request->name.' for self verification.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create Notification When Supervisior Reject Associate
     * Done
     */
    public function SupervisiorAssociateRejectNotification($request, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT_REJECT;
        $data['description'] = 'Supervisior rejected the verification request for '.$request->entity_name.'.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create Notification When Supervisior Reject Employee
     * Done
     */
    public function SupervisiorEmployeeRejectNotification($request, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT_REJECT;
        $data['description'] = 'Supervisior rejected the verification request for '.$request->name.'.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create NOtification when associate reject self verification
     * done
     */
    public function AssociateRejectedNotification($request, $associate, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_USER_COMMENT_REJECT;
        $data['description'] = 'Associate '.$associate->entity_name.' rejected the Self Verification.';
        $data['link'] = $link;
        return $data;
    }

    /**
     *  Create NOtification when employee reject self verification
     * done
     */
    public function EmployeeRejectedNotification($request, $employee, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_USER_COMMENT_REJECT;
        $data['description'] = 'Employee '.$employee->name.' rejected the Self Verification.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Create Notification when Associate approved self Verifiaction
     * Done
     */
    public function AssociateApprovedNotification($request, $associate, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::ASSOCIATE_USER_COMMENT_APPROVED;
        $data['description'] = 'Associate '.$associate->entity_name.' approved the Self Verification.';
        $data['link'] = $link;
        return $data;
    }

    /**
     * Create Notification when Employee approved self Verifiaction
     * Done
     */
    public function EmployeeApprovedNotification($request, $employee, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::EMPLOYEE_USER_COMMENT_APPROVED;
        $data['description'] = 'Employee '.$employee->name.' approved the Self Verification.';
        $data['link'] = $link;
        return $data;
    }

    public function AssociateAcceptedNotification($request, $link)
    {
        $data = request($this->associateCreate);
        $data['title'] = Status::USER_COMMENT_ACTIVE;
        $data['description'] = 'Associate '.$request->entity_name.' has accepted the Terms & Condition and user become active.';
        $data['link'] = $link;
        return $data;
    }



    //
    //==============================================================================================================
    //

    /**
     *  Create New Associate Entry in Maker and Checker
     * Done
     */
    public function NewAssociateCreated($request)
    {
        $data = request($this->makerChecker);
        $data['maker_id'] = $request->user()->id;
        $data['status_id'] = Status::NEW_ASSOCIATE_STATUS;
        $data['maker_comment'] = Status::NEW_ASSOCIATE;
        $data['admin_comment'] = Status::NEW_ASSOCIATE;;
        return $data;
    }

    /**
     * Create New Associate Log Entry in Maker and Checker
     * Done
     */
    public function NewAssociateCreatedLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::NEW_ASSOCIATE_STATUS;
        $data['user_comment'] = Status::NEW_ASSOCIATE;
        return $data;
    }

    /**
     *  Create New Employee Entry in Maker and Checker
     * DOne
     */
    public function NewEmployeeCreated($request)
    {
        $data = request($this->makerChecker);
        $data['maker_id'] = $request->user()->id;
        $data['status_id'] = Status::NEW_EMPLOYEE_STATUS;
        $data['maker_comment'] = Status::NEW_EMPLOYEE;
        $data['admin_comment'] = Status::NEW_EMPLOYEE;;
        return $data;
    }

    /**
     *  Create New Employee Log Entry in Maker and Checker
     * DOne
     */
    public function NewEmployeeCreatedLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::NEW_EMPLOYEE_STATUS;
        $data['user_comment'] = Status::NEW_EMPLOYEE;
        return $data;
    }

    /**
     *      Update New Assocaite Entry in Maker and Checker
     *  Done
     */
    public function SupervisiorAssociateChecker($request,$user)
    {
        $data = request($this->makerChecker);
        $data['checker_id'] = $user->id;
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS;
        $data['admin_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *  Update New Assocaite Entry log in Maker and Checker
     * Done
     */
    public function SupervisiorAssociateCheckerLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *      Update New Assocaite Entry in Maker and Checker
     *  Done
     */
    public function SupervisiorEmployeeChecker($request,$user)
    {
        $data = request($this->makerChecker);
        $data['checker_id'] = $user->id;
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS;
        $data['admin_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *  Update New Assocaite Entry log in Maker and Checker
     * Done
     */
    public function SupervisiorEmployeeCheckerLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *  Supervisior Accept Associate
     *  Done
     */
    public function SupervisiorAssociateApprovedStatus($request)
    {
        $data = request($this->makerChecker);
        $data['is_accept_by_checker'] = '1';
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS_APPROVED;
        $data['admin_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT_APPROVED;
        return $data;
    }

    /**
     *  Supervisior Accept Associate (Log)
     * Done
     */
    public function SupervisiorAssociateApprovedStatusLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS_APPROVED;
        $data['user_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT_APPROVED;
        return $data;
    }

    /**
     *  Supervisior Accept Employee
     *  Done
     */
    public function SupervisiorEmployeeApprovedStatus($request)
    {
        $data = request($this->makerChecker);
        $data['is_accept_by_checker'] = '1';
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS_APPROVED;
        $data['admin_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT_APPROVED;
        return $data;
    }

    /**
     *  Supervisior Accept Employee (Log)
     * Done
     */
    public function SupervisiorEmployeeApprovedStatusLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS_APPROVED;
        $data['user_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT_APPROVED;
        return $data;
    }

    /**
     *      Associate Self Verification
     *  Done
     */
    public function AssociateSelfVerification($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::ASSOCIATE_USER_SUPERVISIOR_STATUS;
        $data['admin_comment'] = Status::ASSOCIATE_USER_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *      Associate Self Verification (Log)
     *  Done
     */
    public function AssociateSelfVerificationLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::ASSOCIATE_USER_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::ASSOCIATE_USER_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *      Employee Self Verification
     *  Done
     */
    public function EmployeeSelfVerification($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::EMPLOYEE_USER_SUPERVISIOR_STATUS;
        $data['admin_comment'] = Status::EMPLOYEE_USER_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *      Employee Self Verification (Log)
     *  Done
     */
    public function EmployeeSelfVerificationLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::EMPLOYEE_USER_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::EMPLOYEE_USER_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *      Associate Reject Status
     *  Done
     */
    public function SupervisiorAssociateRejectedStatus($request)
    {
        $data = request($this->makerChecker);
        $data['is_reject_by_checker'] = '1';
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS_REJECT;
        $data['checker_reject_reason'] = $request->reject_reason;
        $data['admin_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT_REJECT;
        return $data;
    }

    /**
     *      Associate Reject Status(Log)
     *  Done
     */
    public function SupervisiorAssociateRejectedStatusLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS_REJECT;
        $data['user_comment'] = $request->reject_reason;
        return $data;
    }

    /**
     *      Employee Reject Status
     *  Done
     */
    public function SupervisiorEmployeeRejectedStatus($request)
    {
        $data = request($this->makerChecker);
        $data['is_reject_by_checker'] = '1';
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS_REJECT;
        $data['checker_reject_reason'] = $request->reject_reason;
        $data['admin_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT_REJECT;
        return $data;
    }

    /**
     *      Employee Reject Status(Log)
     *  Done
     */
    public function SupervisiorEmployeeRejectedStatusLog($request)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS_REJECT;
        $data['user_comment'] = $request->reject_reason;
        return $data;
    }

    /**
     * Associate Send for Re- Checking
     * Done
     */
    public function SupervisiorAssociateReChecker($request,$user)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS;
        $data['is_reject_by_checker'] = '0';
        $data['admin_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     * Associate Send for Re- Checking (Log)
     * Done
     */
    public function SupervisiorAssociateReCheckerLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     * Associate Send for Re- Checking
     * Done
     */
    public function SupervisiorEmployeeReChecker($request,$user)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS;
        $data['is_reject_by_checker'] = '0';
        $data['admin_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     * Associate Send for Re- Checking (Log)
     * Done
     */
    public function SupervisiorEmployeeReCheckerLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $request->user()->id;
        $data['status_id'] = Status::EMPLOYEE_SUPERVISIOR_STATUS;
        $data['user_comment'] = Status::EMPLOYEE_SUPERVISIOR_COMMENT;
        return $data;
    }

    /**
     *  Associate Approved Self Request
     *  Done
     */
    public function AssociateApprovedSelfDetail($request)
    {
        $data = request($this->makerChecker);
        $data['is_accept_by_user'] = '1';
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_APPROVED;
        $data['admin_comment'] = Status::ASSOCIATE_USER_COMMENT_APPROVED;
        return $data;
    }

    public function UserBSEUploadPending($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::USER_BSE_UPLOAD_STATUS;
        $data['admin_comment'] = Status::USER_BSE_UPLOAD_COMMENT;
        return $data;
    }

    /**
     *  Associate Approved Self Request(Log)
     *  Done
     */
    public function AssociateApprovedSelfDetailLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_APPROVED;
        $data['user_comment'] = Status::ASSOCIATE_USER_COMMENT_APPROVED;
        return $data;
    }

    /**
     *  Employee Approved Self Request
     *  Done
     */
    public function EmployeeApprovedSelfDetail($request)
    {
        $data = request($this->makerChecker);
        $data['is_accept_by_user'] = '1';
        $data['status_id'] = Status::EMPLOYEE_USER_STATUS_APPROVED;
        $data['admin_comment'] = Status::EMPLOYEE_USER_COMMENT_APPROVED;
        return $data;
    }

    /**
     *  Employee Approved Self Request(Log)
     *  Done
     */
    public function EmployeeApprovedSelfDetailLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::EMPLOYEE_USER_STATUS_APPROVED;
        $data['user_comment'] = Status::EMPLOYEE_USER_COMMENT_APPROVED;
        return $data;
    }

    /**
     * Associate Reject Self Request
     * Done
     */
    public function AssociateRejectedSelfDetail($request)
    {
        $data = request($this->makerChecker);
        $data['is_reject_by_user'] = '1';
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_REJECT;
        $data['user_reject_reason'] = $request->reject_reason;
        $data['admin_comment'] = Status::ASSOCIATE_USER_COMMENT_REJECT;
        return $data;
    }

    /**
     * Associate Reject Self Request(Log)
     * Done
     */
    public function AssociateRejectedSelfDetailLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_REJECT;
        $data['user_comment'] = $request->reject_reason;
        return $data;
    }

    /**
     * Employee Reject Self Request
     * Done
     */
    public function EmployeeRejectedSelfDetail($request)
    {
        $data = request($this->makerChecker);
        $data['is_reject_by_user'] = '1';
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_REJECT;
        $data['user_reject_reason'] = $request->reject_reason;
        $data['admin_comment'] = Status::ASSOCIATE_USER_COMMENT_REJECT;
        return $data;
    }

    /**
     * Employee Reject Self Request(Log)
     * Done
     */
    public function EmployeeRejectedSelfDetailLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::EMPLOYEE_USER_STATUS_REJECT;
        $data['user_comment'] = $request->reject_reason;
        return $data;
    }

    /**
     * Send Associate/Employee to Active Status
     * DOne
     */
    public function UserActiveStatus($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::USER_STATUS_ACTIVE;
        $data['admin_comment'] = Status::USER_COMMENT_ACTIVE;
        return $data;
    }

    /**
     * Send Associate/Employee to Active Status(Log)
     * DOne
     */
    public function UserActiveStatusLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::USER_STATUS_ACTIVE;
        $data['user_comment'] = Status::USER_COMMENT_ACTIVE;
        return $data;
    }


    /**
     * Send Associate to Bse UPload Status(Log)
     * DOne
     */
    public function AssociateBSEStatusLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::USER_BSE_UPLOAD_STATUS;
        $data['user_comment'] = Status::USER_BSE_UPLOAD_COMMENT;
        return $data;
    }


    /**
     * Send Employee to Bse UPload Status(Log)
     * DOne
     */
    public function EmployeeBSEStatusLog($request,$user)
    {
        $data = request($this->makerCheckerLog);
        $data['user_id'] = $user->id;
        $data['status_id'] = Status::USER_BSE_UPLOAD_STATUS;
        $data['user_comment'] = Status::USER_BSE_UPLOAD_COMMENT;
        return $data;
    }





    public function SupervisiorRejectSendReupdate($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::ASSOCIATE_SUPERVISIOR_STATUS_REJECT;
        $data['admin_comment'] = Status::ASSOCIATE_SUPERVISIOR_COMMENT_REJECT;
    }










    public function AssociateRejectSendReupdate($request)
    {
        $data = request($this->makerChecker);
        $data['status_id'] = Status::ASSOCIATE_USER_STATUS_REJECT;
        $data['admin_comment'] = Status::ASSOCIATE_USER_COMMENT_REJECT;
    }


    protected function ManageEmployeeLog($log,$ip,$name = '',$model = '')
    {
        $data = [];
        $data['model'] = $model;
        $data['logs'] = $log;
        $data['ip'] = $ip;
        $data['created_by'] = $name;
        return $data;
    }

}
