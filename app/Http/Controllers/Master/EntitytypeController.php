<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\ApiController;
use App\Models\Master\Entitytype;
use Illuminate\Http\Request;

class EntitytypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entitytype = Entitytype::all();

        return $this->showAll($entitytype);
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
     * @param  \App\Models\Entitytype  $entitytype
     * @return \Illuminate\Http\Response
     */
    public function show(Entitytype $entitytype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Entitytype  $entitytype
     * @return \Illuminate\Http\Response
     */
    public function edit(Entitytype $entitytype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entitytype  $entitytype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entitytype $entitytype)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Entitytype  $entitytype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entitytype $entitytype)
    {
        //
    }
}
