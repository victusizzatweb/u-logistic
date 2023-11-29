<?php

namespace App\Http\Controllers;

use App\Models\Driver_license;
use Auth;
use Illuminate\Http\Request;

class DriverLicenseController extends Controller
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
        // Validate the incoming request data
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'certificate_number' => 'required|string',
            'categories' => 'required|string',
            // Add any additional validation rules for other fields
        ]);

        // Get the authenticated user
        $user_id = Auth::id();
        dd($user_id);

        // Create a new instance of the DriverLicense model
        $driverLicense = new Driver_license;

        // Set the attributes of the model
        $driverLicense->user_id = $user_id;
        $driverLicense->certificate_number = $request->input('certificate_number');
        $driverLicense->categories = $request->input('categories');

        // Handle image upload
        $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
        $request->file('image')->storeAs('/public/driverLicense_images/', $image);
        $driverLicense->image = $image;
        

        // Save the model to the database
        $driverLicense->save();

        // Optionally, you can redirect to a different page or return a response
        return redirect()->json([
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
    public function destroy(Driver_license $driver_license)
    {
        //
    }
}
