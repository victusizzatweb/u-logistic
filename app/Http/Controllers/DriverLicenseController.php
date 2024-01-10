<?php

namespace App\Http\Controllers;

use App\Models\Driver_license;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;
class DriverLicenseController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $driverLicense = Driver_license::all();
        return  response()->json([
            "data"=>$driverLicense
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    
    public function store(Request $request)
    {
         $request->validate([
            'certificate_number' => 'required',
            'categories' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);
        $id = Auth::id(); 
        $result = Driver_license::where('user_id',$id)->first();
       if ($result) {
        if ($request->hasFile('image')) {
                    if ($result->image) {
                        Storage::delete('/public/driverLicense_images/' . $result->image);
                    }
                    $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                    $request->file('image')->storeAs('/public/driverLicense_images/', $image);
                    $request->image = $image;
                }
        $result-> update([
            'certificate_number' => $request->input('certificate_number'),
            'categories' => $request->input('categories'),
        ]);
        $result->save();
        return response()->json(['message' => 'Bor edi  yangilandi']);
       }else{
        
        $image = md5(rand(1111,9999).microtime()).'.'.$request->file('image')->extension();
        $request->file('image')->storeAs('/public/driverLicense_images/',$image);
        $auto = Driver_license::create([
            'user_id' => $id,
            'image'=>$image,
            'certificate_number' => $request->input('certificate_number'),
            'categories' => $request->input('categories'),
        ]);

        return response()->json([
            'data'=>$auto,
            'message' => 'Model muvaffaqiyatli yaratildi'],200);
       }
    }


    /**
     * Display the specified resource.
     */
    public function show(Driver_license $driver_license)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Driver_license $driver_license)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver_license $driver_license)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver_license $driver_license,$id)
    {
        $driver_license = Driver_license::findOrFail($id);
          
        $driver_license->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
    
}
