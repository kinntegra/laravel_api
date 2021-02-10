<?php

namespace App\Http\Controllers\External;

use App\Http\Controllers\ApiController;
use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use App\Models\Master\Commercial;
use App\Models\Master\Commercialtype;
use App\Models\Master\Country;
use App\Models\Master\Entitytype;
use App\Models\Master\Relation;
use App\Models\Master\State;
use App\Traits\ManageUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExternalAssociateController extends ApiController
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
        $associate = Associate::find($request->associate_id);

        if($request->status == 5 || $request->status == 6 || $request->status == 7 || $request->status == 8)
        {
            if($request->userstatus == 0)
            {
                $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->AssociateApprovedSelfDetail($request));
                $ass = $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->UserBSEUploadPending($request));
                //$associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->UserActiveStatus($request));
                return $this->showMessage($ass);
            }

            if($request->userstatus == 1)
            {
                $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->AssociateRejectedSelfDetail($request));
            }
        }
        //$associate->associateMakerChecker;
        $associate->status = $associate->associateMakerChecker->status_id;
        return $this->showMessage($associate);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $associate = Associate::find($id);
        $commercials = Commercial::all();
        $commercialtypes = Commercialtype::all();

        if($associate){
            $associate->commercials = $commercials;
            $associate->commercialtypes = $commercialtypes;
            $associate->entitytype_name = Entitytype::firstwhere('id', $associate->entitytype_id)->name;
            if($user = $associate->user)
            {
                $associate->mobile = $user->mobile;
                $associate->email = $user->email;
            }
            if($associate->birth_incorp_date)
            $associate->birth_incorp_date = Carbon::parse($associate->birth_incorp_date)->format('d-m-Y');

            if($associate->gst_validity)
            $associate->gst_validity = Carbon::parse($associate->gst_validity)->format('d-m-Y');

            if($associate->shop_est_validity)
            $associate->shop_est_validity = Carbon::parse($associate->shop_est_validity)->format('d-m-Y');


            if($authorises = $associate->associateAuthorises)
            {
                foreach($authorises as $authorise)
                {
                    if(isset($authorise->aid))
                    {
                        $person = "authorised_person{$authorise->aid}";
                        $email  = "authorised_email{$authorise->aid}";
                        $associate->$person = $authorise->person;
                        $associate->$email = $authorise->email;
                    }

                }
            }

            if($associate->files)
            {
                foreach($associate->files as $file)
                {
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $associate->$name = $value;
                }
            }
        }

        if($address = $associate->address){
            $file = $address->file;
            $name = $file->fieldname;
            $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
            $address->$name = $value;
            $address->country_name = Country::firstWhere('id', $address->country)->name;
            $address->state_name = State::firstWhere('id', $address->state)->name;
        }

        if($banks = $associate->banks){

            foreach($banks as $bank)
            {
                $eval = $bank->is_default;
                $file = $bank->file;
                $name = $file->fieldname;
                $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                $bank->$name = $value;
                if($eval == 1)
                {
                    $associate->ifsc_no = $bank->ifsc_no;
                    $associate->bank_name = $bank->bank_name;
                    $associate->branch_name = $bank->branch_name;
                    $associate->micr = $bank->micr;
                    $associate->account_type = $bank->account_type;
                    $associate->account_no = $bank->account_no;
                    $associate->cheque_upload = $value;
                }elseif($eval == 0){
                    $associate->mfd_ria_ifsc_no = $bank->ifsc_no;
                    $associate->mfd_ria_bank_name = $bank->bank_name;
                    $associate->mfd_ria_branch_name = $bank->branch_name;
                    $associate->mfd_ria_micr = $bank->micr;
                    $associate->mfd_ria_account_type = $bank->account_type;
                    $associate->mfd_ria_account_no = $bank->account_no;
                    $associate->mfd_ria_cheque_upload = $value;
                }
            }
        }
        if($associate->profession_id == 1 || $associate->profession_id == 2 || $associate->profession_id == 3)
        {
            if($licence = $associate->associateLicence){

                $associate->arn_name = $licence->arn_name;
                $associate->arn_rgn_no = $licence->arn_rgn_no;
                $associate->arn_validity = '';
                $associate->euin_name = $licence->euin_name;
                $associate->euin_no = $licence->euin_no;
                $associate->euin_validity = '';
                $associate->ria_name = $licence->ria_name;
                $associate->ria_rgn_no = $licence->ria_rgn_no;
                $associate->ria_validity = '';

                if($licence->arn_validity)
                {
                    $licence->arn_validity = Carbon::parse($licence->arn_validity)->format('d-m-Y');
                    $associate->arn_validity = Carbon::parse($licence->arn_validity)->format('d-m-Y');
                }

                if($licence->euin_validity)
                {
                    $licence->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
                    $associate->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
                }

                if($licence->ria_validity)
                {
                    $licence->ria_validity = Carbon::parse($licence->ria_validity)->format('d-m-Y');
                    $associate->ria_validity = Carbon::parse($licence->ria_validity)->format('d-m-Y');
                }

                if($licence->files)
                {
                    foreach($licence->files as $file)
                    {
                        $name = $file->fieldname;
                        $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                        $licence->$name = $value;
                        $associate->$name = $value;
                    }
                }
            }
        }
        else{
            $introduer = Associate::firstWhere('id', $associate->introducer_id);
            if($licence = $introduer->associateLicence){

                $associate->arn_name = $licence->arn_name;
                $associate->arn_rgn_no = $licence->arn_rgn_no;
                $associate->arn_validity = '';
                $associate->euin_name = $licence->euin_name;
                $associate->euin_no = $licence->euin_no;
                $associate->euin_validity = '';
                $associate->ria_name = $licence->ria_name;
                $associate->ria_rgn_no = $licence->ria_rgn_no;
                $associate->ria_validity = '';

                if($licence->arn_validity)
                {
                    $licence->arn_validity = Carbon::parse($licence->arn_validity)->format('d-m-Y');
                    $associate->arn_validity = Carbon::parse($licence->arn_validity)->format('d-m-Y');
                }

                if($licence->euin_validity)
                {
                    $licence->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
                    $associate->euin_validity = Carbon::parse($licence->euin_validity)->format('d-m-Y');
                }

                if($licence->ria_validity)
                {
                    $licence->ria_validity = Carbon::parse($licence->ria_validity)->format('d-m-Y');
                    $associate->ria_validity = Carbon::parse($licence->ria_validity)->format('d-m-Y');
                }

                if($licence->files)
                {
                    foreach($licence->files as $file)
                    {
                        $name = $file->fieldname;
                        $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                        $licence->$name = $value;
                        $associate->$name = $value;
                    }
                }
            }
        }

        if($certificate = $associate->associateCertificate){
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


            if($associate->associateCertificate->files)
            {
                foreach($certificate->files as $file)
                {
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $certificate->$name = $value;
                }
            }
        }

        if($nominee = $associate->associateNominee){

            if($nominee->nominee_birth_date)
            $nominee->nominee_birth_date = Carbon::parse($nominee->nominee_birth_date)->format('d-m-Y');

            if($address = $nominee->address)
            {
                $nominee->nominee_address1 = $address->address1;
                $nominee->nominee_address2 = $address->address2;
                $nominee->nominee_address3 = $address->address3;
                $nominee->nominee_city = $address->city;
                $nominee->nominee_state = $address->state;
                $nominee->nominee_country = $address->country;
                $nominee->nominee_pincode = $address->pincode;
            }
            if($guardian = $nominee->assoicateGuardian)
            {
                $guardian->guardian_nominee_relation_name = Relation::firstWhere('id', $guardian->guardian_nominee_relation)->name;
                if($guardian->file)
                {
                    $file = $guardian->file;
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $guardian->$name = $value;
                }
                if($address = $guardian->address)
                {
                    $guardian->guardian_address1 = $address->address1;
                    $guardian->guardian_address2 = $address->address2;
                    $guardian->guardian_address3 = $address->address3;
                    $guardian->guardian_city = $address->city;
                    $guardian->guardian_state = $address->state;
                    $guardian->guardian_country = $address->country;
                    $guardian->guardian_pincode = $address->pincode;
                }
            }
        }

        $associate->salesemployees = Employee::where('associate_id', $associate->introducer_id)->get();

        if($commercials = $associate->associateCommercials)
        {
            foreach($commercials as $commercial)
            {
                $comm = Commercial::firstwhere('id',$commercial->commercial_id)->field_name;
                $commtype = Commercialtype::firstwhere('id',$commercial->commercialtype_id)->field_name;
                $name = $comm.'_'.$commtype;
                $associate->$name = $commercial->commercial;
            }
        }
        if($makerChecker = $associate->associateMakerChecker)
        {
            $associate->status = $makerChecker->status_id;
            $associate->makerchecker = $makerChecker;
            if($makerCheckerlog = $associate->associateMakerChecker->makercheckerlogs)
            {
                $associate->makercheckerlog = $makerCheckerlog;
            }
        }
        return $this->showOne($associate);
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
