<?php

namespace App\Http\Controllers;


use App\Models\TexPassport;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TexPassportController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
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
    
    public function store(Request $request)
    {
       
         // Validate the incoming request data
         $data = $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'id_number' => 'required|max:14',
            // Boshqa talablar...
            
        ]);
        $data['user_id'] = Auth::id();
        $texPassport = new TexPassport;
        $texPassport = TexPassport::where("user_id",$data['user_id'])->first();
        
        if ($texPassport) {
            if (Auth::check() && $texPassport->user_id == Auth::id()) {
                // Update the image if provided
                if ($request->hasFile('image')) {
                    // Delete the old image if it exists
                    if ($texPassport->image) {
                        Storage::delete('/public/passport_images/' . $texPassport->image);
                    }
    
                    // Upload the new image
                    $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                    $request->file('image')->storeAs('/public/texPassport_images/', $image);
                    $texPassport->image = $image;
                }
    
                // Update other attributes
                $texPassport->id_number = $data['id_number'];
                $texPassport->user_id = $data['user_id'];
                // Boshqa atributlarni qo'shing...
    
                // Save the changes
                $texPassport->save();
            return response()->json([
                "success"=>"bor edi update qildim",
                "message"=>$texPassport, 201]);
            }
        }else{
            $texPassport = new TexPassport;

            // Set the user_id if the user is authenticated
            if (Auth::check()) {
                $texPassport->user_id = Auth::id();
            }
    
            // Save the image
            $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
            $request->file('image')->storeAs('/public/texPassport_images/', $image);
            $texPassport->image = $image;
    
            // Set other attributes
            $texPassport->id_number = $data['id_number'];
            // Boshqa atributlarni qo'shing...
    
            // Save the Passport instance
            $texPassport->save();
    
            return response()->json([ 
                "message"=> "bu user yoq ekan qoshdim",
                "data"=>$texPassport, 201]);

    
     }
    }

    /**
     * Display the specified resource.
     */
    public function show(TexPassport $texPassport)
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
        $texPassport = TexPassport::findOrFail($id);

        // Check if the authenticated user owns the Passport record
        if (Auth::check() && $texPassport->user_id == Auth::id()) {
            // Update the image if provided
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                if ($texPassport->image) {
                    Storage::delete('/public/texPassport_images/' . $texPassport->image);
                }

                // Upload the new image
                $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                $request->file('image')->storeAs('/public/passport_images/', $image);
                $texPassport->image = $image;
            }

            // Update other attributes
            $texPassport->id_number = $data['id_number'];
            $texPassport->user_id = $data['user_id'];
            // Boshqa atributlarni qo'shing...

            // Save the changes
            $texPassport->save();

            return response()->json([
                "success"=>"update successfuly",
                "message"=>$texPassport, 200]);
        } else {
            return response()->json(['error' => 'You do not have permission to update this Passport record.'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $texPassport = TexPassport::findOrFail($id);
          
        $texPassport->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}
