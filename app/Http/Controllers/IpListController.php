<?php

namespace App\Http\Controllers;

use App\Models\Ip_list;
use App\Http\Requests\StoreIp_listRequest;
use App\Http\Requests\UpdateIp_listRequest;

class IpListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ip_list::all();
    }
    
public function store(StoreIp_listRequest $request)
{
    
    $data = $request->validate([
        'full_address' => 'required',
        'mac_address' => 'required',
        'phone_model' => 'required',
        'last_seen' => 'required', // Assuming last_seen is a datetime
    ]);

    $ipList = new Ip_list;
    $ipList->full_address = $data['full_address'];
    $ipList->mac_address = $data['mac_address'];
    $ipList->phone_model = $data['phone_model'];
    $ipList->last_seen = $data['last_seen'];
    $ipList->save();
//  return "qoshdim";
    return response()->json([
        'message' => 'New IP record created successfully',
        'data' => $ipList,
    ], 201);
}



    /**
     * Display the specified resource.
     */
    public function show(Ip_list $ip_list)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ip_list $ip_list)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIp_listRequest $request, Ip_list $ip_list)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ip_list $ip_list)
    {
        //
    }
}
