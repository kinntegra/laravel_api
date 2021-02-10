<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\ApiController;
use App\Models\Master\Addresstype;
use Illuminate\Http\Request;

class AddresstypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $address_type = Addresstype::all();

        return $this->showAll($address_type);
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
        $newAddressType = Addresstype::create($request->all());

        return $this->showOne($newAddressType, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($addresstype)
    {
        $addressType = Addresstype::findOrFail($addresstype);

        return $this->showOne($addressType);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($addresstype)
    {
        $addressType = Addresstype::findOrFail($addresstype);

        return $this->showOne($addressType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $addresstype)
    {
        $addressType = Addresstype::findOrFail($addresstype);

        $addressType->fill($request->only([
            'name',
        ]));

        if ($addressType->isClean()) {
            return $this->errorResponse('You need to specify any different value to update', 422);
        }

        $addressType->save();

        return $this->showOne($addressType);
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
