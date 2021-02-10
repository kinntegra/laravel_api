<?php

namespace App\Http\Controllers;

use App\Models\Associate\Associate;
use App\Models\Role;
use Illuminate\Http\Request;

class TestController extends ApiController
{
    public function __construct()
    {
        //$this->middleware('client.credentials')->only(['index']);
        parent::__construct();

    }

    public function index(Request $request)
    {
        // $role = ['superadmin','admin'];

        // if($request->user()->hasRole($role))
        // {
        //     $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
        // }else{
        //     if($employee = $request->user()->employee)
        //     {
        //         $id = $employee->associate_id;
        //         $myassociate = Associate::Find($id);
        //         $user = $myassociate->employees()->where('designation_id', '<', $employee->designation_id)->active()->first();

        //         if($user)
        //         {
        //             $user_id = $user->user_id;
        //         }else{
        //             $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
        //         }
        //     }else{
        //         $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
        //     }
        // }
        $role = ['employee','admin'];
        if($request->user()->hasRole($role))
        {
            if($employee = $request->user()->employee)
            {
                $id = $employee->associate_id;
                $myassociate = Associate::Find($id);
                $user_id = $myassociate->user_id;
                $user = $myassociate->employees()->where('designation_id', '<', $employee->designation_id)->active()->first();
                if($user)
                {
                    $user_id = $user->user_id;
                }
            }else{
                $request->user()->associate;
            }
        }else{
            $user_id = Role::where('slug', 'superadmin')->first()->users()->first()->id;
        }
        dd($user_id);
    }
}
