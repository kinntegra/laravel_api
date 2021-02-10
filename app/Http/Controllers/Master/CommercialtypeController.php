<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\ApiController;
use App\Models\Master\Commercialtype;
use Illuminate\Http\Request;

class CommercialtypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commercialtype = Commercialtype::all();

        return $this->showAll($commercialtype);
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
     * @param  \App\Models\Commercialtype  $commercialtype
     * @return \Illuminate\Http\Response
     */
    public function show(Commercialtype $commercialtype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Commercialtype  $commercialtype
     * @return \Illuminate\Http\Response
     */
    public function edit(Commercialtype $commercialtype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Commercialtype  $commercialtype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Commercialtype $commercialtype)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Commercialtype  $commercialtype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commercialtype $commercialtype)
    {
        //
    }
}
