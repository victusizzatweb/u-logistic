<?php

namespace App\Http\Controllers;

use App\Models\AImage;
use App\Models\Announcements;
use App\Models\Role;
use App\Models\User;
use Auth;
use App\Http\Resources\AnnouncementResource;
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
    }
    public function store(Request $request)
{
    $date = now();
    $time = date_format($date,'Y-m-d H:i:s');

    $request->validate([
        'name' => 'required',
        'weight' => 'required',
        'pick_up_address' => 'required',
        'shipping_address' => 'required',
        // 'description' => 'required',
        // 'get_latitude' => 'required',
        // 'get_longitude' => 'required',
        // 'to_go_latitude' => 'required',
        // 'to_go_longitude' => 'required',
    ]);

    $user_id = Auth::id();
    $role = Auth::user()->role_id;

    // Create announcement
    $announcements = Announcements::create([
        'name' => $request->name,
        'weight' => $request->weight,
        'pick_up_address' => $request->pick_up_address,
        'shipping_address' => $request->shipping_address,
        'get_latitude' => $request->get_latitude,
        'get_longitude' => $request->get_longitude,
        'to_go_latitude' => $request->to_go_latitude,
        'to_go_longitude' => $request->to_go_longitude,
        'description' => $request->description,
        'price' => $request->price,
        'time' => $time,
        'user_id' => $user_id,
        'role_id' => $role,
        'status' => 1,
    ]);

    // Check if images are present
    if ($request->hasFile('images')) {
        $imageRules = [
            'images.*' => 'image',
        ];

        $imageValidator = Validator::make($request->all(), $imageRules);

        if ($imageValidator->fails()) {
            return response()->json([
                'messages' => $imageValidator->messages(),
                'error' => 403,
            ]);
        }
        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . "." . $image->extension();
            $image->storeAs('public/announcements/', $path);
            if ($path) {
                AImage::create([
                    'a_id' => $announcements->id,
                    'path' => $path,
                ]);
            } else {
                return response()->json([
                    'error' => 'File upload failed',
                ]);
            }
        }
    }
    $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();

    if ($announcements) {
        $combinedData = $announcements->toArray() + ['images' => $images];
        return response()->json([
            'message' => 'Create successfully',
            'data' => $combinedData,
        ]);
    } else {
        return response()->json([
            'error' => 'Announcement not found',
        ], 403);
    }
}
public function show(Announcements $announcements, $id)
{
    $announcement = Announcements::where('id',$id)->with('user')->first();
    if (!$announcement) {
        return response()->json([
            'error' => 'Not found'
        ], 404);
    }
    $user = User::find($announcement->user_id);
    $responseData = [
        "announcement" => new AnnouncementResource($announcement),
    ];

    return response()->json($responseData);
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
            File::delete(public_path('storage/announcements/'.$Aimage->path));
        }

        AImage::where('a_id', $id)->delete();

        foreach ($request->file('images') as $image) {
            $path = md5(rand(1111, 9999) . microtime()) . "." . $image->extension();
            $image->storeAs('public/announcements/', $path);

            AImage::create([
                'a_id' => $id,
                'path' => $path,
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
        'price'=>$request->price,
        'date' => $date,
        'user_id' => $user_id,
        'role_id' => $role,
        'status' => '1',
    ]);
    $images = AImage::where('a_id', $announcements->id)->pluck('path')->toArray();
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


    public function customer_announcements(Announcements $announcements ,$id)
    { 
       $user = Auth::id();
      
       if( $user && $id == 2){
        $result =  AnnouncementResource::collection(Announcements::where('user_id',$user)->where('status',2)->get());
        return response()->json([
            "data"=>$result
        ],200);
    }
        elseif( $user && $id == 3){
        $result = AnnouncementResource::collection(Announcements::where('user_id',$user)->where('status',4)->get());
        return response()->json([
            "data"=>$result
        ],200);
       }elseif($user && $id == 1){
        $result = AnnouncementResource::collection(Announcements::where('user_id',$user)->get());
        return response()->json([
            "data"=>$result
        ],200);
       }
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
    $userRole = Auth::user()->role_id;
    if ($userRole != '2') {
        AImage::where('a_id', $announcement->id)->delete();
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
