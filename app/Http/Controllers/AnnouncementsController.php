<?php

namespace App\Http\Controllers;

use App\Models\AImage;
use App\Models\Announcements;
use App\Models\Role;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AnnouncementsController extends Controller
{
    public function  __construct(){

        $this->middleware("auth:sanctum");

    }
    public function index()
    {
        $result = Announcements::all();
        return response()->json([
            "data"=>$result
        ]);
    }
    public function store(Request $request)
    {
       
        $date = date('Y-m-d');
        $time = time();
        $request ->validate([
            'name'=>'required',
            'weight'=>'required',
            'pick_up_address'=>'required',
            'shipping_address'=>'required',
            'description'=>'required',
            'get_latitude'=>'required',
            'get_longitude'=>'required',
            'to_go_latitude'=>'required',
            'to_go_longitude'=>'required',
        ]);
        
        $imageRules = array(
            'images' => 'image',
        );
        if (is_array($request->images)) {
            $imageCount = count($request->images);
            for ($i = 0; $i < $imageCount; $i++) {
                $image = ['images' => $request->images[$i]];
                $imageValidator = Validator::make($image, $imageRules);
                if ($imageValidator->fails()) {
                    $messages = $imageValidator->messages();
                    return response()->json([
                        "messages" => $messages,
                        'error' => 403
                    ]);
                }
            }
        } else {
            return response()->json([
                'error' => 'Invalid data format for images',
            ]);
        }

        $user_id = Auth::id();
        $role = Auth::user()->role_id;
        
        if($role != 'Haydovchi'){
            $announcements= Announcements::create([
                'name' => $request->name,
                'weight'=> $request->weight,
                'pick_up_address'=> $request->pick_up_address,
                'shipping_address'=> $request->shipping_address,
                'get_latitude' => $request->get_latitude,
                'get_longitude'=> $request->get_longitude,
                'to_go_latitude'=> $request->to_go_latitude,
                'to_go_longitude'=> $request->to_go_longitude,
                'date'=> $date,
                'description'=> $request->description,
                'time'=> $time,
                'user_id'=>$user_id,
                'role_id'=>$role,
                'status'=>'1',
               ]);
            $imageCount = count($request->images);

            for ($i = 0; $i < $imageCount; $i++) {
                $image = $request->images[$i];
                $path = md5(rand(1111,9999).microtime()).".".$image->extension();
                // Generate a unique filename (Laravel will automatically handle this)
                $image->storeAs('public/announcements/',$path);

                // Check if the file was uploaded successfully
                if ($path) {
                    AImage::create([
                        'a_id' => $announcements->id,
                        'path' => '/storage/announcements/'.$path,
                    ]);
                } else {
                    // Handle file upload failure
                    return response()->json([
                        'error' => 'File upload failed',
                    ]);
                }
            }
            //  $images = AImage::where('a_id',$announcements->id)->get();
             $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();
                // dd($images);
                 if ($announcements && $images) {
                     $combinedData = $announcements->toArray() + ['images' => $images];
                     
                     return response()->json([ 
                        "message"=>"create successfully",
                         'data' => $combinedData,
                     ]);
                 } else {
                     return response()->json([
                         'error' => 'Announcement or images not found',
                     ],403);
                 }
             
        }else{
            return response()->json([ 
                'error'=>404,
            ]);
        }
    
    }
    public function show(Announcements $announcements,$id)
    {

       
        $announcements = Announcements::find($id);
        $images = AImage::where('a_id', $id)->pluck('path')->toArray();
    //    dd($images);
        if ($announcements && $images) {
            $combinedData = $announcements->toArray() + ['images' => $images];
            
            return response()->json([ 
                'data' => $combinedData,
            ]);
        } else {
            return response()->json([
                'error' => 'Announcement or images not found',
            ]);
        }
    }
    public function update(Request $request, Announcements $announcements, $id)
    {
    $announcements = Announcements::find($id);
    $date = now();
    $user_id = Auth::id();
    $role = Auth::user()->role_id;

    $request->validate([
        'name' => 'required',
        'weight' => 'required',
        'pick_up_address' => 'required',
        'shipping_address' => 'required',
        'description' => 'required',
        'get_latitude' => 'required',
        'get_longitude' => 'required',
        'to_go_latitude' => 'required',
        'to_go_longitude' => 'required',
    ]);

    if ($request->has('images')) {
        $imageRules = [
            'images.*' => 'image|max:2048',
        ];

        $imageValidator = Validator::make($request->all(), $imageRules);

        if ($imageValidator->fails()) {
            return response()->json([
                'error' => 403,
                'messages' => $imageValidator->messages(),
            ]);
        }

        $images = AImage::where('a_id', $id)->get();

        foreach ($images as $Aimage) {
            File::delete(public_path('storage/announcements/' . $Aimage->path));
        }

        AImage::where('a_id', $id)->delete();

        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . "." . $image->extension();
            $image->storeAs('public/announcements/', $path);

            AImage::create([
                'a_id' => $id,
                'path' => '/storage/announcements/'.$path,
            ]);
        }
    }
    // dd($request);
    $announcements->update([
        'name' => $request->name,
        'weight' => $request->weight,
        'pick_up_address' => $request->pick_up_address,
        'shipping_address' => $request->shipping_address,
        'get_latitude' => $request->get_latitude,
        'get_longitude' => $request->get_longitude,
        'to_go_latitude' => $request->to_go_latitude,
        'to_go_longitude' => $request->to_go_longitude,
        'description' => $request->description,
        'date' => $date,
        'user_id' => $user_id,
        'role_id' => $role,
        'status' => '1',
    ]);
    // dd($announcements);
    $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();
                // dd($images);
                 if ($announcements && $images) {
                     $combinedData = $announcements->toArray() + ['images' => $images];
                     
                     return response()->json([ 
                         'data' => $combinedData,
                     ]);
                 } else {
                     return response()->json([
                         'error' => 'Announcement or images not found',
                     ],403);
                 }
}


    public function customer_announcements(Announcements $announcements)
    {
        $id = Auth::id();
       $result = Announcements::where('user_id',$id)->get();
       
       return response()->json([
        "data"=>$result
    ]);
    }
    public function destroy($id)
{
    // Find the announcement by ID
    $announcement = Announcements::find($id);

    // Check if the announcement exists
    if (!$announcement) {
        return response()->json([
            'error' => 'Announcement not found',
        ], 404);
    }

    // Check if the authenticated user has the right role to delete
    $userRole = Auth::user()->role_id;
    if ($userRole != 'Haydovchi') {
        // Delete the associated images first
        AImage::where('a_id', $announcement->id)->delete();

        // Then delete the announcement
        $announcement->delete();

        return response()->json([
            'message' => 'Announcement and associated images deleted successfully',
        ]);
    } else {
        return response()->json([
            'error' => 'You do not have permission to delete announcements',
        ], 403);
    }
}

}
