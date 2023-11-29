<?php

namespace App\Http\Controllers;
use App\Models\MyAuto;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MyAutoController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
    public function index(){
        $auto = MyAuto::all();
        return $auto;
    }
    public function store(Request $request)
    {
       
        $data = $request->validate([
            'tex_passport_number' => 'required',
            'transport_model' => 'required',
            'transport_capacity' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);
        $data['user_id'] = Auth::id(); // Set the user ID
        // dd($data);
        // dd(auth()->user()->$request->user_id);
        $image = md5(rand(1111,9999).microtime()).'.'.$request->file('image')->extension();
        $request->file('image')->storeAs('/public/myAuto/',$image);
        // $auto->user_id = $data['user_id']
        $auto = new MyAuto;
        $auto->user_id = $data['user_id'];
        $auto->image = $image;
        $auto->tex_passport_number = $data['tex_passport_number'];
        $auto->transport_model = $data['transport_model'];
        $auto->transport_capacity = $data['transport_capacity'];
        $auto->save();
        return response()->json($auto, 201);
    }
    public function update(Request $request, $id)
{
    $auto = MyAuto::findOrFail($id);
    
    $data = $request->validate([
        'tex_passport_number' => 'required',
        'transport_model' => 'required',
        'transport_capacity' => 'required',
        'image' => 'sometimes|required|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
    ]);

    // Update user_id if needed
    if (Auth::check()) {
        $data['user_id'] = Auth::id();
    }

    // Update image if provided
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($auto->image) {
            Storage::delete('/public/myAuto/' . $auto->image);
        }

        // Upload the new image
        $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
        $request->file('image')->storeAs('/public/myAuto/', $image);
        $auto->image = $image;
    }

    // Update other fields
    $auto->tex_passport_number = $data['tex_passport_number'];
    $auto->transport_model = $data['transport_model'];
    $auto->transport_capacity = $data['transport_capacity'];

    // Save the changes
    $auto->save();

    return response()->json([
       "success" => true,
        "data" =>$auto, 200]);
}
    
    public function show(MyAuto $myAuto)
    {
        return response()->json($myAuto, 200);
    }

    public function destroy($id)
    {
     
            $myAuto = MyAuto::findOrFail($id);
          
            $myAuto->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
            
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'An error occurred while deleting the user.'
        //     ], 500);
        // }
    }
}
