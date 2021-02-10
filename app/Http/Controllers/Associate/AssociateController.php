<?php

namespace App\Http\Controllers\Associate;

use App\Http\Controllers\ApiController;
use App\Models\Address;
use App\Models\Associate\Associate;
use App\Models\Bank;
use App\Models\Associate\Employee;
use App\Models\File;
use App\Models\Master\Commercial;
use App\Models\Master\Commercialtype;
use App\Models\Master\Country;
use App\Models\Master\Entitytype;
use App\Models\Master\State;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use App\Services\MyServices;
use App\Services\Security;
use App\Traits\ManageAddress;
use Illuminate\Http\Request;
use App\Traits\ManageAssociate;
use App\Traits\ManageBank;
use App\Traits\ManageFile;
use App\Traits\ManageUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssociateController extends ApiController
{
    use ManageAssociate, ManageUser, ManageAddress, ManageBank, ManageFile;

    private $address, $user, $bank, $notification;
    public function __construct(Address $address, User $user, Bank $bank, Notification $notification)
    {
        //$this->middleware('client.credentials')->only(['index']);
        parent::__construct();
        $this->address = $address;
        $this->user = $user;
        $this->bank = $bank;
        $this->notification = $notification;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //if($request->user()->in_house == 1)
        //{
            if($request->active == 0)
            {//orderBy('id')
                $associates = Associate::all();
            }else{
                $associates = Associate::active()->get();
            }
            foreach($associates as $associate)
            {
                $user = $associate->user;
                $authorise = $associate->associateAuthorises()->orderBy('aid', 'ASC')->first();
                $address = $associate->address;
                $associate->entity_code = Entitytype::firstwhere('id', $associate->entitytype_id)->name;
                $associate->mobile = $user->mobile;
                $associate->email = $user->email;
                $associate->user_active = $user->is_active;
                $associate->user_first = $user->is_first;
                $associate->user_inhouse = $user->in_house;
                $associate->last_login_at = $user->last_login_at;
                $associate->last_login_ip = $user->last_login_ip;
                if($authorise)
                {
                    $associate->name = $authorise->person;
                }else{
                    $associate->name = null;
                }

                if($associate->associateMakerChecker)
                {
                    $associate->status = $associate->associateMakerChecker->status_id;
                    $associate->status_code = $associate->associateMakerChecker->admin_comment;
                }

                if($address)
                {
                    $associate->location = $address->city;
                }else{
                    $associate->location = '';
                }

            }
        //}
        return $this->showAll($associates);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $associates = Associate::all();

        // return $this->showAll($associates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('pan_no'))
        {
            //Create Or Update User
            $user = $this->user->updateOrCreate(['username' => MyServices::getEncryptedString(strtoupper($request->pan_no))], $this->createUser($request));

            //Assign Role to User
            if(!$user->hasRole([Associate::ASSOCIATE_ADMIN_ROLE]))
            {
                $user->roles()->attach([$this->getRoleID(Associate::ASSOCIATE_ADMIN_ROLE)]);
            }
            //return $this->showMessage($this->createAssociate($request));
            //Create Or Update New Assocaite
            $associate = $user->associate()->updateOrCreate(['pan_no' => MyServices::getEncryptedString(strtoupper($request->pan_no))], $this->createAssociate($request));

            if($request->step == 3)
            {
                //RND Start


                //RND End
                if(!empty($request->authorised_person1) && !empty($request->authorised_email1))
                {
                    $associate->associateAuthorises()->updateOrCreate(['associate_id' => $associate->id,'aid' => Associate::FIRST_AUTHORISE],$this->createAssociateAuthorise($request,Associate::FIRST_AUTHORISE));
                }
                if(!empty($request->authorised_person2) && !empty($request->authorised_email2))
                {
                    $associate->associateAuthorises()->updateOrCreate(['associate_id' => $associate->id,'aid' => Associate::SECOND_AUTHORISE],$this->createAssociateAuthorise($request,Associate::SECOND_AUTHORISE));
                }
                if(!empty($request->authorised_person3) && !empty($request->authorised_email3))
                {
                    $associate->associateAuthorises()->updateOrCreate(['associate_id' => $associate->id,'aid' => Associate::THIRD_AUTHORISE],$this->createAssociateAuthorise($request,Associate::THIRD_AUTHORISE));
                }
                if($request->hasFile(Associate::ASSOCIATE_PHOTO))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_PHOTO, 'fileable_type' => Associate::ASSOCIATE_MODEL],$this->uploadSingleFile($request, Associate::ASSOCIATE_PHOTO, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_PAN))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_PAN, 'fileable_type' => Associate::ASSOCIATE_MODEL],$this->uploadSingleFile($request, Associate::ASSOCIATE_PAN, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_AADHAR))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_AADHAR, 'fileable_type' => Associate::ASSOCIATE_MODEL],$this->uploadSingleFile($request, Associate::ASSOCIATE_AADHAR, $associate->id));
                }
                //$associate->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_UPLOAD, $associate->id));
                //$associate->associateAuthorises()->createMany($this->createAssociateAuthorise($request));
            }
            if($request->step == 4)
            {
                $address = $associate->address()->updateOrCreate(['addressable_id' => $associate->id,'addresstype_id' => Associate::DEFAULT_ADDRESS_TYPE],$this->createAddress($request));

                if($request->hasFile(Address::ADDRESS_UPLOAD))
                {
                    $address->file()->updateOrCreate(['fileable_id' => $address->id,'fieldname' => Address::ADDRESS_UPLOAD],$this->uploadSingleFile($request, Address::ADDRESS_UPLOAD, $associate->id, $address->id));
                }
                //$address = $associate->address()->save($this->address->make($this->createAddress($request)));
                //$address->file()->create($this->uploadSingleFile($request, Address::ADDRESS_UPLOAD, $associate->id));
            }
            if($request->step == 5)
            {
                $bank = $associate->banks()->updateOrCreate(['bankable_id' => $associate->id,'is_default' => BANK::BANK_DEFAULT],$this->createBank($request));

                if($request->hasFile(Bank::BANK_UPLOAD))
                {
                    $bank->file()->updateOrCreate(['fileable_id' => $bank->id,'fieldname' => Bank::BANK_UPLOAD],$this->uploadSingleFile($request, Bank::BANK_UPLOAD, $associate->id, $bank->id));
                }
                if(!empty($request->mfd_ria_ifsc_no))
                {
                    $otherbank = $associate->banks()->updateOrCreate(['bankable_id' => $associate->id,'is_default' => BANK::BANK_NOT_DEFAULT],$this->createOtherBank($request));

                    if($request->hasFile(Bank::BANK_OTHER_UPLOAD))
                    {
                        $otherbank->file()->updateOrCreate(['fileable_id' => $otherbank->id,'fieldname' => Bank::BANK_OTHER_UPLOAD],$this->uploadSingleFile($request, Bank::BANK_OTHER_UPLOAD, $associate->id, $otherbank->id));
                    }
                }
                //$bank = $associate->bank()->save($this->bank->make($this->createBank($request)));
                //$bank->file()->create($this->uploadSingleFile($request, Bank::BANK_UPLOAD, $associate->id));
            }
            if($request->step == 6)
            {
                if($request->hasFile(Associate::ASSOCIATE_LOGO))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_LOGO],$this->uploadSingleFile($request, Associate::ASSOCIATE_LOGO, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_GST))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_GST],$this->uploadSingleFile($request, Associate::ASSOCIATE_GST, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_SHOP_EST))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_SHOP_EST],$this->uploadSingleFile($request, Associate::ASSOCIATE_SHOP_EST, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_PD))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_PD],$this->uploadSingleFile($request, Associate::ASSOCIATE_PD, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_PD_ASL))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_PD_ASL],$this->uploadSingleFile($request, Associate::ASSOCIATE_PD_ASL, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_PD_COI))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_PD_COI],$this->uploadSingleFile($request, Associate::ASSOCIATE_PD_COI, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_CO_MOA))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_CO_MOA],$this->uploadSingleFile($request, Associate::ASSOCIATE_CO_MOA, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_CO_AOA))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_CO_AOA],$this->uploadSingleFile($request, Associate::ASSOCIATE_CO_AOA, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_CO_COI))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_CO_COI],$this->uploadSingleFile($request, Associate::ASSOCIATE_CO_COI, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_CO_ASL))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_CO_ASL],$this->uploadSingleFile($request, Associate::ASSOCIATE_CO_ASL, $associate->id));
                }
                if($request->hasFile(Associate::ASSOCIATE_CO_BR))
                {
                    $associate->files()->updateOrCreate(['fileable_id' => $associate->id,'fieldname' => Associate::ASSOCIATE_CO_BR],$this->uploadSingleFile($request, Associate::ASSOCIATE_CO_BR, $associate->id));
                }

                //$associate->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_MORE_UPLOAD, $associate->id));
            }
            if($request->step == 7)
            {
                if($request->profession_id == 1 || $request->profession_id == 2 || $request->profession_id == 3)
                {
                    $associateLicence = $associate->associateLicence()->updateOrCreate(['associate_id' => $associate->id],$this->createAssociateLicence($request));
                    if($request->hasFile(Associate::ARN_UPLOAD))
                    {
                        $associateLicence->files()->updateOrCreate(['fileable_id' => $associateLicence->id,'fieldname' => Associate::ARN_UPLOAD],$this->uploadSingleFile($request, Associate::ARN_UPLOAD, $associate->id, $associateLicence->id));
                    }
                    if($request->hasFile(Associate::EUIN_UPLOAD))
                    {
                        $associateLicence->files()->updateOrCreate(['fileable_id' => $associateLicence->id,'fieldname' => Associate::EUIN_UPLOAD],$this->uploadSingleFile($request, Associate::EUIN_UPLOAD, $associate->id, $associateLicence->id));
                    }
                    if($request->hasFile(Associate::RIA_UPLOAD))
                    {
                        $associateLicence->files()->updateOrCreate(['fileable_id' => $associateLicence->id,'fieldname' => Associate::RIA_UPLOAD],$this->uploadSingleFile($request, Associate::RIA_UPLOAD, $associate->id, $associateLicence->id));
                    }
                }
                //$associateLicence->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_LICENCE_UPLOAD, $associate->id));
            }
            if($request->step == 8)
            {
                $associateCertificate = $associate->associateCertificate()->updateOrCreate(['certificateable_id' => $associate->id,'certificateable_type' => Associate::ASSOCIATE_MODEL],$this->createAssociateCertificate($request));
                if($request->hasFile(Associate::NISM_VA_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::NISM_VA_UPLOAD],$this->uploadSingleFile($request, Associate::NISM_VA_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::NISM_XA_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::NISM_XA_UPLOAD],$this->uploadSingleFile($request, Associate::NISM_XA_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::NISM_XB_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::NISM_XB_UPLOAD],$this->uploadSingleFile($request, Associate::NISM_XB_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::CFP_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::CFP_UPLOAD],$this->uploadSingleFile($request, Associate::CFP_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::CWM_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::CWM_UPLOAD],$this->uploadSingleFile($request, Associate::CWM_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::CA_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::CA_UPLOAD],$this->uploadSingleFile($request, Associate::CA_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::CS_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::CS_UPLOAD],$this->uploadSingleFile($request, Associate::CS_UPLOAD, $associate->id, $associateCertificate->id));
                }
                if($request->hasFile(Associate::COURSE_UPLOAD))
                {
                    $associateCertificate->files()->updateOrCreate(['fileable_id' => $associateCertificate->id,'fieldname' => Associate::COURSE_UPLOAD],$this->uploadSingleFile($request, Associate::COURSE_UPLOAD, $associate->id, $associateCertificate->id));
                }
                //$associateCertificate = $associate->associateCertificate()->create($this->createAssociateCertificate($request));
                //$associateCertificate->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_CERTIFICATE_UPLOAD, $associate->id));
            }
            if($request->step == 9)
            {
                $nominee = $associate->associateNominee()->updateOrCreate(['associate_id' => $associate->id],$this->createAssociateNominee($request));
                $address = $nominee->address()->updateOrCreate(['addressable_id' => $nominee->id,'addresstype_id' => Associate::DEFAULT_ADDRESS_TYPE],$this->createNomineeAddress($request));
                //$nominee = $associate->associateNominee()->create($this->createAssociateNominee($request));
                //$nominee->address()->save($this->address->make($this->createNomineeAddress($request)));
            }
            if($request->step == 10)
            {
                $nominee = $associate->associateNominee;

                $guardian = $nominee->assoicateGuardian()->updateOrCreate(['associate_nominee_id' => $nominee->id],$this->createAssoicateGuardian($request));
                $guardian->address()->updateOrCreate(['addressable_id' => $guardian->id,'addresstype_id' => Associate::DEFAULT_ADDRESS_TYPE],$this->createGuardianAddress($request));

                if($request->hasFile(Associate::GUARDIAN_PAN_UPLOAD))
                {
                    $guardian->file()->updateOrCreate(['fileable_id' => $guardian->id,'fieldname' => Associate::GUARDIAN_PAN_UPLOAD],$this->uploadSingleFile($request, Associate::GUARDIAN_PAN_UPLOAD, $associate->id, $guardian->id));
                }
                // $guardian = $nominee->assoicateGuardian()->create($this->createAssoicateGuardian($request));
                // $guardian->address()->save($this->address->make($this->createGuardianAddress($request)));
                // $guardian->file()->create($this->uploadSingleFile($request, Associate::GUARDIAN_PAN_UPLOAD, $associate->id));
            }
            if($request->step == 11)
            {
                //dd($this->createAssociateCommercial($request));
                $commercial = Commercial::pluck('field_name');
                $commercialtype = Commercialtype::pluck('field_name');

                $i = 0;

                foreach($commercial as $comm){
                    $data = array();
                    foreach($commercialtype as $type){
                        $data[] = $comm."_".$type;

                    }

                    $title = ["commercial_id", "commercialtype_id", "commercial"];
                    $final = '';

                    foreach($data as $name)
                    {
                        if($request->has($name) && $request->$name > 0)
                        {
                            $value = $request->$name;
                            $join = $name."_".$value; //Join Name and Value
                            $split[] = explode('_', $join); //Split Name and Value
                            $split[$i][0] = Commercial::where('field_name', $split[$i][0])->first()->id;
                            $split[$i][1] = Commercialtype::where('field_name', $split[$i][1])->first()->id;
                            $final = array_combine(
                                $title,
                                $split[$i]
                            );
                            $comm = $final['commercial_id'];
                            $i++;
                            $associate->associatecommercials()->updateOrCreate(['associate_id' => $associate->id, 'commercial_id' => $comm], $final);

                        }

                    }
                }
                //Suervisior Accept Case
                if($request->status == 2 && $request->userstatus == 0)
                {
                    $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->SupervisiorAssociateApprovedStatus($request));
                }
                //Supervisior Reject Case
                if($request->status == 2 && $request->userstatus == 1)
                {
                    $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->SupervisiorAssociateRejectedStatus($request));
                }
                //Rejected Status (Supervisior & Employee)
                if($request->status == 4 || $request->status == 7)
                {
                    $associate->associateMakerChecker()->updateOrCreate(['makercheckerable_id' => $associate->id,'makercheckerable_type' => Associate::ASSOCIATE_MODEL],$this->SupervisiorAssociateReChecker($request,$user));
                }
                //return $this->showMessage($this->createAssociateCommercial($request));
                //$associate->associatecommercials()->createMany($this->createAssociateCommercial($request));
            }


            return $this->showOne($associate, 201);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Associate $associate)
    {
        //$associate = Associate::findOrFail($id);
        if($associate){
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
            if($address->file)
            {
                $file = $address->file;
                $name = $file->fieldname;
                $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                $address->$name = $value;
            }
        }

        if($banks = $associate->banks){

            foreach($banks as $bank)
            {
                $eval = $bank->is_default;
                if($bank->file)
                {
                    $file = $bank->file;
                    $name = $file->fieldname;
                    $value = env('APP_URL').'/'.$file->path.'/'.$file->name;
                    $bank->$name = $value;
                }

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

        $associate->salesemployees = Employee::where('associate_id', $associate->introducer_id)->where('department_id',1)->get();

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
        if($request->hasFile('photo_upload'))
        {
            return $this->showMessage($request->introduced_by);
        }
        return $this->showMessage($request->all());
        if($request->has('pan_no'))
        {
            //User Created
            $user = $this->user->updateOrCreate(['username' => MyServices::getEncryptedString(strtoupper($request->pan_no))], $this->createUser($request));
            //Assign Role to User
            if(!$user->hasRole([Associate::ASSOCIATE_ADMIN_ROLE]))
            {
                $user->roles()->attach([$this->getRoleID(Associate::ASSOCIATE_ADMIN_ROLE)]);
            }

            $associate = $user->associate()->updateOrCreate(['id' => $request->associate_id], $this->createAssociate($request));

            $associate->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_UPLOAD, $associate->id));

            return $this->showMessage($request->all());



            $address = $associate->address()->save($this->address->make($this->createAddress($request)));
            $address->file()->create($this->uploadSingleFile($request, Address::ADDRESS_UPLOAD, $associate->id));

            $bank = $associate->bank()->save($this->bank->make($this->createBank($request)));
            $bank->file()->create($this->uploadSingleFile($request, Bank::BANK_UPLOAD, $associate->id));

            $associateLicence = $associate->associateLicence()->create($this->createAssociateLicence($request));
            $associateLicence->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_LICENCE_UPLOAD, $associate->id));

            $associateCertificate = $associate->associateCertificate()->create($this->createAssociateCertificate($request));
            $associateCertificate->files()->createMany($this->uploadMultipleFile($request, Associate::ASSOCIATE_CERTIFICATE_UPLOAD, $associate->id));

            $nominee = $associate->associateNominee()->create($this->createAssociateNominee($request));
            $nominee->address()->save($this->address->make($this->createNomineeAddress($request)));

            $guardian = $nominee->assoicateGuardian()->create($this->createAssoicateGuardian($request));
            $guardian->address()->save($this->address->make($this->createGuardianAddress($request)));
            $guardian->file()->create($this->uploadSingleFile($request, Associate::GUARDIAN_PAN_UPLOAD, $associate->id));



            $associate->associatecommercials()->createMany($this->createAssociateCommercial($request));

            return $this->showOne($user, 201);
        }else{
            return $this->showMessage($request->all());
        }
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
        $associate = Associate::find($id);
        $associate->mobile = $associate->user->mobile;
        $associate->email = $associate->user->email;
        if($licence = $associate->associateLicence)
        {
            $associate->arn = $licence->arn_rgn_no;
            $associate->euin = $licence->euin_no;
        }
        if($address = $associate->address)
        {
            $associate->address1 = $address->address1;
            $associate->address2 = $address->address2;
            $associate->address3 = $address->address3;
            $associate->city = $address->city;
            $associate->state = State::firstWhere('id', $address->state)->code;
            $associate->country = Country::firstWhere('id', $address->country)->name;
            $associate->pincode = $address->pincode;
        }
        //dd(Security::decryptData($associate->bse_password));
        //$associate->user;
        if ($associate != null)
        {
            $data = str_replace('{1}', 'DEALER', $data);
            $data = str_replace('{2}', $associate->associate_code, $data);
            $data = str_replace('{3}', 'CORPBRANCH', $data);
            $data = str_replace('{4}', Security::decryptData($associate->bse_password), $data);
            $data = str_replace('{5}', $associate->entity_name, $data);
            $data = str_replace('{6}', $associate->associate_code, $data);
            $data = str_replace('{7}', $associate->arn, $data);
            $data = str_replace('{8}', $associate->euin, $data);
            $data = str_replace('{9}', str_replace( ',', ' ', $associate->address1 ), $data);
            $data = str_replace('{10}', str_replace( ',', ' ', $associate->address2 ), $data);
            $data = str_replace('{11}', str_replace( ',', ' ', $associate->address3 ), $data);
            $data = str_replace('{12}', $associate->city, $data);
            $data = str_replace('{13}', $associate->state, $data);
            $data = str_replace('{14}', $associate->pincode, $data);
            $data = str_replace('{15}', $associate->country, $data);
            $data = str_replace('{16}', $associate->telephone, $data);
            $data = str_replace('{17}', $associate->mobile, $data);
            $data = str_replace('{18}', '', $data);
            $data = str_replace('{19}', $associate->email, $data);
            $data = str_replace('{20}', 'F', $data);
        }
        dd($data);
        if (Storage::exists('/associatedocuments/' . $id . '/' . $associate->associate_code . '.csv'))
        {
            Storage::delete('/associatedocuments/' . $id . '/' . $associate->associate_code . '.csv');
        }

        Storage::put('/associatedocuments/' . $id . '/' . $associate->associate_code . '.csv', $data);

        $url = Storage::url('/associatedocuments/' . $id . '/' . $associate->associate_code . '.csv');
        return $this->showMessage($url);
    }

    public function getLogs($id)
    {
        $associate = Associate::find($id);
        $associate->logs = $associate->associateMakerChecker->makercheckerlogs;
        return $this->showOne($associate);
    }
}
