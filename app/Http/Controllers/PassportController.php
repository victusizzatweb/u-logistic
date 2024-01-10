<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PassportController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $passport = Passport::all();
        return response()->json([ 
            "data"=>$passport],200);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
       
         // Validate the incoming request data
         $data = $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'id_number' => 'required|max:14',
            // Boshqa talablar...
            
        ]);
        $data['user_id'] = Auth::id();
        // Create a new Passport instance
        $passport = new Passport;

        // Set the user_id if the user is authenticated
        // if (Auth::check()) {
        //     $passport->user_id = Auth::id();
        // }
        $passport = Passport::where("user_id",$data['user_id'])->first();
        
        if ($passport) {
            if (Auth::check() && $passport->user_id == Auth::id()) {
                // Update the image if provided
                if ($request->hasFile('image')) {
                    // Delete the old image if it exists
                    if ($passport->image) {
                        Storage::delete('/public/passport_images/' . $passport->image);
                    }
    
                    // Upload the new image
                    $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                    $request->file('image')->storeAs('/public/passport_images/', $image);
                    $passport->image = $image;
                }
    
                // Update other attributes
                $passport->id_number = $data['id_number'];
                $passport->user_id = $data['user_id'];
                // Boshqa atributlarni qo'shing...
    
                // Save the changes
                $passport->save();
            return response()->json([
                "success"=>"bor edi update qildim",
                "message"=>$passport],200);
            }
        }else{
            $passport = new Passport;

            // Set the user_id if the user is authenticated
            if (Auth::check()) {
                $passport->user_id = Auth::id();
            }
    
            // Save the image
            $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('/public/passport_images/', $image);
            $passport->image = $image;
    
            // Set other attributes
            $passport->id_number = $data['id_number'];
            // Boshqa atributlarni qo'shing...
    
            // Save the Passport instance
            $passport->save();
    
            return response()->json([ 
                "message"=> "bu user yoq ekan qoshdim",
                "data"=>$passport],200);

    
     }
    }

    
    public function show(Passport $passport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Passport $passport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'image' => 'sometimes|required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'id_number' => 'required|max:14',
            // Boshqa talablar...
        ]);
        $data['user_id'] = Auth::id();

        // Find the Passport instance by ID
        $passport = Passport::findOrFail($id);

        // Check if the authenticated user owns the Passport record
        if (Auth::check() && $passport->user_id == Auth::id()) {
            // Update the image if provided
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($passport->image) {
                    Storage::delete('/public/passport_images/' . $passport->image);
                }

                // Upload the new image
                $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                $request->file('image')->storeAs('/public/passport_images/', $image);
                $passport->image = $image;
            }

            // Update other attributes
            $passport->id_number = $data['id_number'];
            $passport->user_id = $data['user_id'];
            // Boshqa atributlarni qo'shing...

            // Save the changes
            $passport->save();

            return response()->json([
                "success"=>"Update User Successfuly",
                "message"=>$passport, 200]);
        } else {
            return response()->json(['error' => 'You do not have permission to update this Passport record.'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $passport = Passport::findOrFail($id);
        dd($passport);
          if($passport){
            $passport->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
          }else{
            return response()->json([
                'success' => false,
                'message' => 'User mavjud emas.'
            ]);
          }
       
    }
}
