<?php

namespace App\Providers;

use App\Mail\AssociateNotification;
use App\Mail\SupervisiorNotification;
use App\Models\Associate\Associate;
use App\Models\Associate\AssociateCommercial;
use App\Models\Associate\Certificate;
use App\Models\Associate\Employee;
use App\Models\Associate\MakerChecker;
use App\Models\Role;
use App\Models\User;
use App\Services\MyServices;
use App\Traits\ManageAssociate;
use App\Traits\ManageUser;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AssociateServiceProvider extends ServiceProvider
{
    use ManageUser, ManageAssociate;
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        Associate::created(function($associate) use ($request){
            $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';
            $maker = $associate->associateMakerChecker()->Create($this->NewAssociateCreated($request));
            $maker->makercheckerlogs()->create($this->NewAssociateCreatedLog($request));
            $request->user()->notification()->create($this->NewAssociateCreation($request,$link));
        });

        Employee::created(function($employee) use ($request){
            $link = 'associate/'.MyServices::getencryptNo($request->associate_id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';
            $maker = $employee->employeeMakerChecker()->Create($this->NewEmployeeCreated($request));
            $maker->makercheckerlogs()->create($this->NewEmployeeCreatedLog($request));
            $request->user()->notification()->create($this->NewEmployeeCreation($request,$link));
        });

        Associate::updated(function($associate) use ($request){
            if($associate->bse_upload == 1 && $associate->is_active == 1 && $associate->is_credential_email == 1)
            {
                $myassociate = Associate::find($request->associate_id);
                $myassociate->is_credential_email = 0;
                $myassociate->save();

                $makerchecker = $associate->associateMakerChecker;

                $user = User::firstwhere('id',$makerchecker->maker_id);

                //$makerchecker->makercheckerlogs()->create($this->AssociateBSEStatusLog($request,$user));

                $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->UserActiveStatus($request));

                $makerchecker->makercheckerlogs()->create($this->UserActiveStatusLog($request,$user));

                $link = '/login';
                //Active The User
                $associate_user = User::firstwhere('id', $associate->user_id);
                $associate_user->is_active = 1;
                $associate_user->save();

                $entitytype = $associate->entitytype_id;
                $text = 'Date of Incorporation';
                if($entitytype == 4)
                {
                    $text = 'Date of Birth';
                }
                $subject = 'Kinntegra Credentials';
                $data = [
                    'name' => $associate->entity_name,
                    'link' => env('APP_WEBURL').'/'.$link,
                    'message1' => 'Congrats! Associate '. $associate->entity_name . ' has been approved.',
                    'message2' => 'Your password is first five letter of your PANCARD in Capital and day & month of '.$text. '. Click Below to login',
                ];
                $user = $associate->user;
                $email = $user->email;

                Mail::to($email)->send(new SupervisiorNotification($data, $subject));
            }
        });

        Employee::updated(function($employee) use ($request){
            if($employee->bse_upload == 1 && $employee->is_active == 1 && $employee->is_credential_email == 1)
            {
                $myemployee = Employee::find($request->employee_id);
                $myemployee->is_credential_email = 0;
                $myemployee->save();
                $link = '/login';
                $makerchecker = $employee->employeeMakerChecker;

                $user = User::firstwhere('id',$makerchecker->maker_id);

                $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->UserActiveStatus($request));

                $makerchecker->makercheckerlogs()->create($this->UserActiveStatusLog($request,$user));
                //Active The User
                $employee_user = User::firstwhere('id', $employee->user_id);
                $employee_user->is_active = 1;
                $employee_user->save();

                $text = 'Date of Birth';
                $subject = 'Kinntegra Credentials';
                $data = [
                    'name' => $employee->name,
                    'link' => env('APP_WEBURL').'/'.$link,
                    'message1' => 'Congrats! Employee '. $employee->name . ' has been approved.',
                    'message2' => 'Your password is first five letter of your PANCARD in Capital and day & month of '.$text. '. Click Below to login',
                ];
                $user = $employee->user;
                $email = $user->email;

                Mail::to($email)->send(new SupervisiorNotification($data, $subject));
            }
        });


        //Send an Email to Supervisior For New Associate Created
        AssociateCommercial::created(function($associateCommercial) use ($request){
            $associate = Associate::find($request->associate_id);
            $user_name = $request->user()->name;

            $count = AssociateCommercial::where('associate_id',$associate->id)->get()->count();

            if($count == 1)
            {
                $logs = $this->ManageAssociateLog(json_encode($request->all()),$request->ip(), $user_name);
                $associate->associateLogs()->create($logs);
                $role = ['superadmin','admin'];

                if($request->user()->hasRole($role))
                {
                    $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
                }else{
                    if($employee = $request->user()->employee)
                    {
                        $id = $employee->associate_id;
                        $myassociate = Associate::Find($id);
                        $user = $myassociate->employees()->where('designation_id', '<', $employee->designation_id)->active()->first();
                        if($user)
                        {
                            $user_id = $user->user_id;
                        }else{
                            $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
                        }
                    }else{
                        $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
                    }
                }

                $user = User::firstwhere('id', $user_id);
                //dd($user);
                if($user)
                {
                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';
                    $maker = $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->SupervisiorAssociateChecker($request,$user));
                    $maker->makercheckerlogs()->create($this->SupervisiorAssociateCheckerLog($request,$user));
                    $user->notification()->create($this->SupervisiorAssociateVerification($request,$link));


                    $data = [
                        //'name' => $associate->entity_name,
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'New Associate '. $associate->entity_name . ' has been created successfully by ' . $request->user()->name .'.',
                        'message2' => 'Please verify the same by cilck on below link.',
                    ];
                    $subject = 'New Associate Verification';
                    $email = $user->email;
                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));

                }
            }
        });

        //Send an Email to Supervisior For New Employee Created
        Certificate::created(function($certificate) use ($request){
            $associate = Associate::find($request->associate_id);

            $employee = Employee::find($certificate->certificateable_id);
            //dd($employee);
            if($request->employee_edit == 0)
            {
                // $role = ['employee','admin'];
                // if($request->user()->hasRole($role))
                // {
                //     if($employee = $request->user()->employee)
                //     {
                //         $id = $employee->associate_id;
                //         $myassociate = Associate::Find($id);
                //         $user_id = $myassociate->user_id;
                //         $user = $myassociate->employees()->where('designation_id', '<', $employee->designation_id)->active()->first();
                //         if($user)
                //         {
                //             $user_id = $user->user_id;
                //         }
                //     }else{
                //         $request->user()->associate;
                //     }
                // }else{
                //     $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
                // }

                $user = User::firstwhere('id', $request->supervisor_id);

                if($user)
                {
                    $link = 'associate/'.MyServices::getencryptNo($request->associate_id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';
                    $maker = $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeChecker($request,$user));
                    $maker->makercheckerlogs()->create($this->SupervisiorEmployeeCheckerLog($request,$user));
                    $user->notification()->create($this->SupervisiorEmployeeVerification($request,$link));


                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'New Employee '. $employee->name . ' has been created successfully by ' . $request->user()->name .'.',
                        'message2' => 'Please verify the same by cilck on below link.',
                    ];
                    $subject = 'New Employee Verification';
                    $email = $user->email;
                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }
            }
        });

        MakerChecker::updated(function($makerchecker) use ($request){
            $associate = Associate::find($request->associate_id);


            if($makerchecker->status_id == 2 && $request->status == 1)
            {
                if($request->has('associate_edit') && $request->associate_edit == 1)
                {

                }

                if($request->has('employee_edit') && $request->employee_edit == 1)
                {
                    $user = User::firstwhere('id', $request->supervisor_id);
                    $employee = Employee::find($request->employee_id);
                    if($user)
                    {
                        $link = 'associate/'.MyServices::getencryptNo($request->associate_id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';
                        //$maker = $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->SupervisiorEmployeeChecker($request,$user));
                        $makerchecker->makercheckerlogs()->create($this->SupervisiorEmployeeCheckerLog($request,$user));
                        $user->notification()->create($this->SupervisiorEmployeeVerification($request,$link));


                        $data = [
                            'name' => $user->name,
                            'link' => env('APP_WEBURL').'/'.$link,
                            'message1' => 'New Employee '. $employee->name . ' has been created successfully by ' . $request->user()->name .'.',
                            'message2' => 'Please verify the same by cilck on below link.',
                        ];
                        $subject = 'New Employee Verification';
                        $email = $user->email;
                        Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                    }
                }
            }

            if($makerchecker->is_accept_by_checker == 1 && $makerchecker->status_id == 3 && $request->status == 2)
            {
                if($request->has('associate_edit'))
                {
                    $makerchecker->makercheckerlogs()->create($this->SupervisiorAssociateApprovedStatusLog($request));

                    $maker = $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->AssociateSelfVerification($request));
                    $maker->makercheckerlogs()->create($this->AssociateSelfVerificationLog($request));

                    $link = 'external-associate/'.MyServices::getencryptNo($associate->id);
                    $request->user()->notification()->create($this->AssociateSelfNotification($request,$link));

                    $user = $associate->user;
                    $email = $user->email;
                    $subject = "Associate : ".$associate->entity_name." Self Verification";
                    $data = [
                        'name' => $associate->entity_name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Please verify your details carefully by click on below link.',
                        'message2' => 'Let us know if any changes are required.',
                    ];
                    Mail::to($email)->send(new SupervisiorNotification($data,$subject));
                }
                if($request->has('employee_edit'))
                {
                    $employee = Employee::find($request->employee_id);

                    $makerchecker->makercheckerlogs()->create($this->SupervisiorEmployeeApprovedStatusLog($request));

                    $maker = $employee->employeeMakerChecker()->updateOrCreate(['makercheckerable_id' => $employee->id,'makercheckerable_type' => Employee::EMPLOYEE_MODEL],$this->EmployeeSelfVerification($request));
                    $maker->makercheckerlogs()->create($this->EmployeeSelfVerificationLog($request));

                    $link = 'external-employee/'.MyServices::getencryptNo($employee->id);
                    $request->user()->notification()->create($this->EmployeeSelfNotification($request,$link));

                    $user = $employee->user;
                    $email = $user->email;
                    $subject = "Employee : ".$employee->name." Self Verification";
                    $data = [
                        'name' => $employee->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Please verify your details carefully by click on below link.',
                        'message2' => 'Let us know if any changes are required.',
                    ];
                    Mail::to($email)->send(new SupervisiorNotification($data,$subject));
                }
            }

            if($makerchecker->is_reject_by_checker == 1 && $makerchecker->status_id == 4 && $request->status == 2)
            {
                if($request->has('associate_edit'))
                {
                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->SupervisiorAssociateRejectedStatusLog($request));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';

                    $user->notification()->create($this->SupervisiorAssociateRejectNotification($request,$link));

                    $subject = 'Associate '. $associate->entity_name . 'has been Rejected.';
                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Associate '. $associate->entity_name . ' has been Rejected by Supervisior ' . $request->user()->name .'.',
                        'message2' => 'Please update the same as per rejected reason',
                        'reject_reason' => $request->reject_reason,
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }
                if($request->has('employee_edit'))
                {
                    $employee = Employee::find($request->employee_id);

                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->SupervisiorEmployeeRejectedStatusLog($request));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';

                    $user->notification()->create($this->SupervisiorEmployeeRejectNotification($request,$link));

                    $subject = 'Employee '. $employee->name . 'has been Rejected.';
                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Employee- '. $employee->name . ' has been Rejected by Supervisior- ' . $request->user()->name .'.',
                        'message2' => 'Please update the same as per rejected reason',
                        'reject_reason' => $request->reject_reason,
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }

            }

            if(($makerchecker->status_id == 2 && $request->status == 4) || ($makerchecker->status_id == 2 && $request->status == 7))
            {
                if($request->has('associate_edit'))
                {
                    $user = User::firstwhere('id', $makerchecker->checker_id);

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';

                    $makerchecker->makercheckerlogs()->create($this->SupervisiorAssociateReCheckerLog($request,$user));

                    $request->user()->notification()->create($this->SupervisiorAssociateReVerification($request,$associate,$link));

                    $subject = 'Re-Verify Associate : '.$associate->entity_name;

                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Associate '. $associate->entity_name . ' has been Updated successfully by ' . $request->user()->name .'.',
                        'message2' => 'Please verify the same by click on below button.',
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data,$subject));
                }

                if($request->has('employee_edit'))
                {
                    $employee = Employee::find($request->employee_id);

                    $user = User::firstwhere('id', $makerchecker->checker_id);

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';

                    $makerchecker->makercheckerlogs()->create($this->SupervisiorEmployeeReCheckerLog($request,$user));

                    $request->user()->notification()->create($this->SupervisiorEmployeeReVerification($request,$employee,$link));

                    $subject = 'Re-Verify Employee : '.$employee->name;

                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Employee'. $employee->name . ' has been Updated successfully by ' . $request->user()->name .'.',
                        'message2' => 'Please verify the same by click on below button.',
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data,$subject));
                }
            }

            if($makerchecker->is_reject_by_user == 1 && $makerchecker->status_id == 7)
            {
                if($request->has('associate_edit'))
                {
                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->AssociateRejectedSelfDetailLog($request,$user));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';

                    $user->notification()->create($this->AssociateRejectedNotification($request,$associate,$link));

                    $subject = 'Associate '. $associate->entity_name . 'has Self Rejected.';
                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Associate '. $associate->entity_name . ' has been Rejected by Self Associate.',
                        'message2' => 'Please update the same as per rejected reason',
                        'reject_reason' => $request->reject_reason,
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }

                if($request->has('employee_edit'))
                {
                    $employee = Employee::find($request->employee_id);

                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->EmployeeRejectedSelfDetailLog($request,$user));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';

                    $user->notification()->create($this->EmployeeRejectedNotification($request,$associate,$link));

                    $subject = 'Employee '. $employee->name . 'has been Rejected.';
                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Employee- '. $employee->name . ' has self Rejected.',
                        'message2' => 'Please update the same as per rejected reason',
                        'reject_reason' => $request->reject_reason,
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }

            }

            if($makerchecker->is_accept_by_user == 1 && $makerchecker->status_id == 6)
            {
                $associate = Associate::find($request->associate_id);
                if($request->has('associate_edit'))
                {
                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->AssociateApprovedSelfDetailLog($request,$user));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/edit';

                    $user->notification()->create($this->AssociateApprovedNotification($request,$associate,$link));

                    $subject = 'Associate '. $associate->entity_name . 'has Self Approved.';
                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Associate '. $associate->entity_name . ' has been Approved by Self Associate.',
                        'message2' => 'Please upload the Associate details to BSE.',
                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));

                }

                if($request->has('employee_edit'))
                {
                    $employee = Employee::find($request->employee_id);

                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->EmployeeApprovedSelfDetailLog($request,$user));

                    $link = 'associate/'.MyServices::getencryptNo($associate->id).'/employee/'.MyServices::getencryptNo($employee->id).'/edit';

                    $user->notification()->create($this->EmployeeApprovedNotification($request,$employee,$link));

                    $subject = 'Employee '. $employee->name . 'has Self Approved.';

                    $data = [
                        'name' => $user->name,
                        'link' => env('APP_WEBURL').'/'.$link,
                        'message1' => 'Verification of Associate '. $employee->name . ' has been Approved by Self Employee.',
                        'message2' => 'Credentials will be share soon with the Client',

                    ];
                    $email = $user->email;

                    Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                }
            }

            if($makerchecker->status_id == 8)
            {


                // if($request->has('associate_edit'))
                // {
                //     //Active The Associate
                //     $associate->is_active = 1;
                //     $associate->save();
                //     //Active The User
                //     $associate_user = User::firstwhere('id', $associate->user_id);
                //     $associate_user->is_active = 1;
                //     $associate_user->save();

                //     $entitytype = $associate->entitytype_id;
                //     $text = 'Date of Incorporation';
                //     if($entitytype == 4)
                //     {
                //         $text = 'Date of Birth';
                //     }
                //     $subject = 'Kinntegra Credentials';
                //     $data = [
                //         'name' => $associate->entity_name,
                //         'link' => env('APP_WEBURL').'/'.$link,
                //         'message1' => 'Associate'. $associate->entity_name . ' has accepted the request.',
                //         'message2' => 'Your password is first five letter of your PANCARD in Capital and day & month of '.$text. '. Click Below to login',
                //     ];
                //     $user = $associate->user;
                //     $email = $user->email;

                //     Mail::to($email)->send(new SupervisiorNotification($data, $subject));

                // }
                if($request->has('employee_edit'))
                {
                    $associate = Associate::find($request->associate_id);

                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->UserActiveStatusLog($request,$user));

                    $link = '/login';

                    $employee = Employee::find($request->employee_id);
                    if($employee->department_id > 1)
                    {
                        //Active The Associate
                        $employee->is_active = 1;
                        $employee->save();
                        //Active The User
                        $employee_user = User::firstwhere('id', $employee->user_id);
                        $employee_user->is_active = 1;
                        $employee_user->save();

                        $text = 'Date of Birth';
                        $subject = 'Kinntegra Credentials';
                        $data = [
                            'name' => $employee->name,
                            'link' => env('APP_WEBURL').'/'.$link,
                            'message1' => 'Employee'. $employee->name . ' has accepted the request.',
                            'message2' => 'Your password is first five letter of your PANCARD in Capital and day & month of '.$text. '. Click Below to login',
                        ];
                        $user = $employee->user;
                        $email = $user->email;

                        Mail::to($email)->send(new SupervisiorNotification($data, $subject));
                    }

                }
            }

            if($makerchecker->status_id == 10)
            {
                if($request->has('associate_edit'))
                {
                    $associate = Associate::find($request->associate_id);

                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->AssociateBSEStatusLog($request,$user));
                }

                if($request->has('employee_edit'))
                {
                    $associate = Associate::find($request->associate_id);
                    $employee = Employee::find($request->employee_id);
                    $user = User::firstwhere('id',$makerchecker->maker_id);

                    $makerchecker->makercheckerlogs()->create($this->EmployeeBSEStatusLog($request,$user));
                }

            }

        });
    }
}
