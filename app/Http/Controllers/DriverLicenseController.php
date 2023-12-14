<?php

namespace App\Http\Controllers;

use App\Models\Driver_license;
use Illuminate\Http\Request;
use Auth;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        // Validate the incoming request data
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificate_number' => 'required',
            'categories' => 'required',
            // Add any additional validation rules for other fields
        ]);

        // Get the authenticated user
        $user_id = Auth::id();
        // dd($user_id);
        $driverLicense = Driver_license::where("user_id",$user_id)->first();
        // dd($driverLicense);

        // Create a new instance of the DriverLicense model
        $driverLicense = new Driver_license;

        // Set the attributes of the model
        $driverLicense->user_id = $user_id;
        $driverLicense->certificate_number = $request->input('certificate_number');
        $driverLicense->categories = $request->input('categories');

        // Handle image upload
        $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
        $request->file('image')->storeAs('/public/driverLicense_images/', $image);
        $driverLicense->image = '/storage/driverLicense_images/'.$image;
        

        // Save the model to the database
        $driverLicense->save();
        // dd($driverLicense);
        // Optionally, you can redirect to a different page or return a response
        return response()->json([
            "success"=> 'Driver license created successfully',
            "data"=>$driverLicense
        ]);
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
