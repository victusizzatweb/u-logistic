<?php

namespace App\Http\Controllers;
use App\Http\Resources\MyAutoResource;
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
        $all = MyAutoResource::collection(MyAuto::all());
        return $all;
    }
    public function store(Request $request)
    {
        
    //    dd($request);
         $request->validate([
            'transport_number' => 'required',
            'transport_model' => 'required',
            'transport_capacity' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);
        $id = Auth::id(); 
        $result = MyAuto::where('user_id',$id)->first();
       if ($result) {
        if ($request->hasFile('image')) {
                    // Delete the old image if it exists
                    if ($result->image) {
                        Storage::delete('/public/myAuto/' . $result->image);
                    }
            
                    // Upload the new image
                    $image = md5(rand(1111, 9999) . microtime()) . '.' . $request->file('image')->extension();
                    $request->file('image')->storeAs('/public/myAuto/', $image);
                    $request->image = $image;
                }
        $result-> update([
            
            'transport_number' => $request->input('transport_number'),
            'transport_model' => $request->input('transport_model'),
            'transport_capacity'=>$request->input('transport_capacity')
        ]);
        $result->save();
        return response()->json(['message' => 'Bor edi  yangilandi']);
       }else{
        
        $image = md5(rand(1111,9999).microtime()).'.'.$request->file('image')->extension();
        $request->file('image')->storeAs('/public/myAuto/',$image);
        $auto = MyAuto::create([
            'user_id' => $id,
            'image'=>$image,
            'transport_number' => $request->input('transport_number'),
            'transport_model' => $request->input('transport_model'),
            'transport_capacity'=>$request->input('transport_capacity')
        ]);

        return response()->json([
            'data'=>$auto,
            'message' => 'Model muvaffaqiyatli yaratildi'],200);
       }
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
            
    }
}
