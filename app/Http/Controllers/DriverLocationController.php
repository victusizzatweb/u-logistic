<?php

namespace App\Http\Controllers;

use App\Models\DriverLocation;
use Illuminate\Http\Request;

class DriverLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = DriverLocation::with('user', 'myAuto')->get();
        $responseData = $result->map(function ($request) {
            return [
                'id' => $request->id,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'location_time'=>$request->location_time,
                'updated_at'=>date_format($request->updated_at,'Y-m-d H:i:s'),
                'user' => $request->user ? $request->user->only(['id', 'fullname', 'phone', 'image']) : null,
                'myAuto' => $request->myAuto ? $request->myAuto->only(['user_id', 'transport_model', 'transport_capacity', 'transport_number','image']) : null,
            ];
        });
        return response()->json(['data' => $responseData], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function driverLocationData($id)
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = $request->input('user_id');

        // $userId orqali user_id bo'yicha modelni topish
        $driverLocation = DriverLocation::where('user_id', $userId)->first();

        if ($driverLocation) {
            // Agar topilsa, modelni yangilash
            $driverLocation->update([
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'location_time'=>$request->input('location_time')
            ]);

            return response()->json(['message' => 'Model muvaffaqiyatli yangilandi']);
        } else {
            // Agar topilmagan bo'lsa, yangi modelni yaratish
            $newDriverLocation = DriverLocation::create([
                'user_id' => $userId,
                'longitude' => $request->input('longitude'),
                'latitude' => $request->input('latitude'),
                'location_time'=>$request->input('location_time')
            ]);

            return response()->json([
                'data'=>$newDriverLocation,
                'message' => 'Model muvaffaqiyatli yaratildi'],200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DriverLocation $driverLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DriverLocation $driverLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DriverLocation $driverLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DriverLocation $driverLocation)
    {
        //
    }
}
