<?php

namespace App\Http\Controllers;

use App\Models\CoHub;
use Illuminate\Http\Request;

class CoHubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Firstname'=>'required',
            'Lastname'=>'required',
            'phone'=>'required',
        ]);
        $coHub = CoHub::create([
          'Firstname'=>$request->Firstname,
          'Lastname'=>$request->Lastname,
          'phone'=>$request->phone
        ]);
        $coHub->save();

        // Return the stored data in JSON format
        return response()->json($coHub);
    }

    /**
     * Display the specified resource.
     */
    public function show(CoHub $coHub)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CoHub $coHub)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CoHub $coHub)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CoHub $coHub)
    {
        //
    }
}
