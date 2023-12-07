<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use App\Models\APimage;
use App\Models\Role;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class AnnouncementsController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // $now = Carbon::now();
        $date =date('Y-m-d');
        $time = date('H:i:s');
        // $diff = $now->diffForHumans($date);
        // dd($date,$time);
        $request ->validate([
            'name'=>'required',
            'weight'=>'required',
            'pick_up_address'=>'required',
            'shipping_address'=>'required',
            'description'=>'required',
        ]);
        
        $imageRules=array(
            'images' => 'image|max:2048',
        );
        // dd($request->images);
        foreach ($request->images as $image) {
           $image = array('image'=>$image);
            $imageValidator = Validator::make($image, $imageRules);
            if ($imageValidator->fails()) {
                $messages = $imageValidator->messages();
                // return redirect(route("product.create"))->withErrors($messages);
                return redirect(route("announcements.store"))->withErrors($messages);
            }
        }
        $user_id = Auth::id();
        $role = Auth::user()->role_id;
        
        if($role != 'Haydovchi'){
            $announcements= Announcements::create([
                'name' => $request->name,
                'weight'=> $request->weight,
                'pick_up_address'=> $request->pick_up_address,
                'shipping_address'=> $request->shipping_address,
                'date'=> $date,
                'description'=> $request->description,
                'time'=> $time,
                'user_id'=>$user_id,
                'role_id'=>$role,
                'status'=>'1',
               ]);
            //    dd($request->image);
            if (!is_array($request->images) && !is_object($request->images)) {
                return response()->json([ 
                    'error'=>404,
                ]);
            } else {
                foreach ($request->images as $image) {
                    $path = md5(rand(1111,9999).microtime()).".".$image->extension();
                $image->storeAs('public/announcements/',$path);
                // dd($image);
                 $images =  APimage::create([
                    'a_id' => $announcements->id,
                    'path'=>$path,
                ]);
              
                }
            }
              
        
             
              
               return response()->json([ 
                   'success'=>"create successfuly",
                   'data'=>$announcements,
                //    "data1"=>$images
               ]);
        }else{
            return response()->json([ 
                'error'=>404,
            ]);
        }
    
    }

    

    /**
     * Display the specified resource.
     */
    public function show(Announcements $announcements)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcements $announcements)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcements $announcements)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcements $announcements)
    {
        //
    }
}
