<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Client\LeadRequest;
use App\Models\Client\ClientAccount;
use App\Models\Client\Lead;
use App\Traits\ManageAddress;
use App\Traits\ManageLead;
use Illuminate\Http\Request;

class LeadController extends ApiController
{

    use ManageLead, ManageAddress;
    public function __construct()
    {
        //parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leads = Lead::all();

        return $this->showAll($leads);
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
    public function store(LeadRequest $request)
    {
        $lead = ClientAccount::updateOrCreate(['id' => $request->id],$this->createLead($request));
        $lead->address()->updateOrCreate(['addressable_id' => $lead->id,'addressable_type' => ClientAccount::DEFAULT_ADDRESS_TYPE],$this->createAddress($request));
        $lead->address;
        return $this->showOne($lead);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {

        $lead = '';
        if($request->has('mobile') && !empty($request->mobile))
        {
            if($id == 0)
            {
                $lead = Lead::firstWhere('mobile', $request->mobile);
            }else{
                $lead = Lead::where('mobile', $request->mobile)->where('id','!=',$id)->first();
            }

        }
        if($request->has('email') && !empty($request->email))
        {
            if($id == 0)
            {
                $lead = Lead::firstWhere('email', $request->email);
            }else{
                $lead = Lead::where('email', $request->email)->where('id','!=',$id)->first();
            }
        }
        //
        // if (is_numeric($id)){

        //     $lead = Lead::firstWhere('mobile', $id);
        // }
        // if(filter_var($id, FILTER_VALIDATE_EMAIL)) {
        //     $lead = Lead::firstWhere('email', $id);
        // }
        return $this->showMessage($lead ? true : false);
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
