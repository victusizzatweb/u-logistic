<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\Driver_licenseResource;
use App\Http\Resources\MyAutoResource;
use App\Http\Resources\TexPassportResource;
use App\Models\Announcements;
use App\Models\Driver_license;
use App\Models\DriverLocation;
use App\Models\DriverRequest;
use App\Models\MyAuto;
use App\Models\Passport;
use App\Models\TexPassport;
use App\Models\User;
use Illuminate\Http\Request;

class DriverRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getDriverRequestData($id)
{
    $driverRequest = DriverRequest::where('announcement_id', $id)->with('user', 'myAuto')->get();
    if (!$driverRequest) {
        return response()->json(['message' => 'Announcement request not found'], 404);
    }

    $relatedRequests = DriverRequest::where('announcement_id', $id)->where('status',1)->with('user', 'myAuto')->get();
    $responseData = $relatedRequests->map(function ($request) {
        $myAutoimage = asset('storage/myAuto') . '/';
        $image = asset('storage/user').'/';
        return [
            'id' => $request->id,
            'driver_id' => $request->driver_id,
            'announcement_id' => $request->announcement_id,
            'time' => $request->time,
            'user' => $request->user ?[
                'id'=>$request->user->id, 
                'fullname'=>$request->user->fullname,
                'phone'=>$request->user->phone,
                'image'=>$image.$request->user->image
                  ]: null,
            'myAuto' => $request->myAuto ?[
                'id' => $request->myAuto->id,
                'user_id' => $request->myAuto->user_id,
                'transport_number' => $request->myAuto->transport_number,
                'transport_model' => $request->myAuto->transport_model,
                'transport_capacity' => $request->myAuto->transport_capacity,
                'image' => $myAutoimage . $request->myAuto->image,
            ]:null,
        ];
    });

    return response()->json(['data' => $responseData], 200);
}

    public function store(Request $request)
    {
       
        $request->validate([
            'driver_id' => 'required',
            'announcement_id' => 'required',
        ]);
        $date = now();
        $time = date_format($date, 'Y-m-d H:i:s');
        if(!DriverRequest::where('driver_id',$request->driver_id)->where('announcement_id',$request->announcement_id)->first()){
            $requests = DriverRequest::create([
                'driver_id' => $request->driver_id,
                'announcement_id' => $request->announcement_id,
                'time'=>$time
            ]);
            
            return response()->json([
                'message' => 'Request created successfully',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Request  qoldirilgan',
            ],403);
        }
        
    }
    public function acceptanceRequest($id)
    {
        $driverRequest = DriverRequest::find($id);
        $driverRequest ->update([
            'status'=>2
        ]);
        if ($driverRequest) {
            $announcementId = $driverRequest->announcement_id;
            $driverId = $driverRequest->driver_id;
            $announcement = Announcements::where('id',$announcementId)->first();
            $driver = User::find($driverId);

            $announcement->update([
               'status'=>2,
               'driver_id'=>$driverId
            ]);
            $driver->update([
                'status'=>3,
             ]);
            return response()->json([
                'data'=> $announcement,
            ],200);
            
        } else {
            return response()->json([
                'error'=>'Not found Request'
            ],404);
        }
        
    }

    public function CancleRequest(Request $request)
    {
        $driverRequest = DriverRequest::where('announcement_id',$request->announcement_id)->where('status',2)->first();
        $announcement = Announcements::where('id',$request->announcement_id)->first();
        if($driverRequest && $announcement){
            $driverRequest->update([
                'status'=>3
            ]);
            $announcement->update([
                'status'=>1,
                'driver_id'=>null
             ]);
             return response()->json([
                 'data'=> $announcement,
             ],200);
         } else {
            return response()->json([
                'error'=>'Not found Request'
            ],404);
        }
    }
    // status boyicha driverRequestlarni loishni qilib qoyish 
    //Annoustmentni update qilish atkaz berilgan Requstga qarab
    public function complate_announcements($id){
        $driverActive = Announcements::where('driver_id',$id)->where('status',4)->get();
       
        if (!$driverActive) {
            return response()->json([
                'error' => 'Announcement not found',
            ]);
        }
        return  AnnouncementResource::collection($driverActive);
    }
    public function new_announcements(){
        $driverActive = Announcements::where('status',1)->get();
       
       
        if (!$driverActive) {
            return response()->json([
                'error' => 'Announcement not found',
            ]);
        }
        return  AnnouncementResource::collection($driverActive);
    }
    public function active_announcements($id){
        $announcement = Announcements::where('driver_id', $id)->whereIn('status', [2,3])->with('user')->get();
        if ($announcement) {
            return response()->json( AnnouncementResource::collection($announcement));
            
        } else {
    return response()->json(['error' => 'Not found'], 404);
}

     
   
      }

      public function all_announcements($id){
        $driverActive =  Announcements::where('driver_id',$id)->get();
        if($driverActive){
          return AnnouncementResource::collection($driverActive);
        }else{
          return response()->json([
              "message"=>'Data not found'
          ],405);
        }
      }

      

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DriverRequest $driverRequest)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function driver_data($id){
        $myAuto = MyAuto::where('user_id', $id)->first();
        $driverLicense = Driver_license::where('user_id', $id)->first();
        $texPassport = TexPassport::where('user_id', $id)->first();
        $passport = Passport::where('user_id', $id)->first();
            $myAutoimage = asset('storage/myAuto') . '/';
            $myAutoData =$myAuto? [
                'id' => $myAuto->id,
                'user_id' => $myAuto->user_id,
                'transport_number' => $myAuto->transport_number,
                'transport_model' => $myAuto->transport_model,
                'transport_capacity' => $myAuto->transport_capacity,
                'image' => $myAutoimage . $myAuto->image,
            ]:null;
            $driverLicenseImage = asset('storage/driverLicense_images') . '/';
            $driverLicense1 =$driverLicense? [
                'id' => $driverLicense->id,
                'user_id' => $driverLicense->user_id,
                'certificate_number' => $driverLicense->certificate_number,
                'categories' => $driverLicense->categories,
                'image' => $driverLicenseImage . $driverLicense->image,
            ]:null;
            $texPassportImage = asset('storage/texPassport_images') . '/';
            $texPassport1 = $texPassport?[
                'id' => $texPassport->id,
                'user_id' => $texPassport->user_id,
                'id_number' => $texPassport->id_number,
                'image' => $texPassportImage . $texPassport->image,
            ]:null;
            $passportImage = asset('storage/passport_images') . '/';
            $passport1 = $passport?[
                'id' => $passport->id,
                'user_id' => $passport->user_id,
                'id_number' => $passport->id_number,
                'image' => $passportImage . $passport->image,
            ]:null;
            $driverInfo = [
            'myAuto' => $myAutoData? $myAutoData:null,
            'driverLicense' => $driverLicense1 ? $driverLicense1:null,
            'texPassport' => $texPassport1? $texPassport1:null,
            'passport' => $passport1 ? $passport1:null,
        ];
            return response()->json($driverInfo,200);
    }
    public function confirmation_announcement($id){
        
        $announcements = Announcements::where('id',$id)->where('status',3)->first();
        if($announcements){
            $announcement = Announcements::find($id);
            $user_id = $announcement->driver_id;
            $user = User::find($user_id);
            $announcement->update([
                'status'=>4
            ]);
            $user->update([
                'status'=>2
            ]);
            return response()->json([
                'message'=>'Topshiriq muvaffaqiyatli bajarildi'
            ],200);
        }else{
            return response()->json([
                'error'=>'Anouncement not found'
            ],404);
        }
    }

    public function finish_announcement($id){
        $driverRequest = Announcements::find($id);
        $driverRequest->update([
               'status'=>3,
        ]);
        

        return response()->json($driverRequest,200);
    }
    public function DriverDataAnnouncement($id){
        $announcement  = Announcements::find($id);
        $driver_id = $announcement->driver_id;
        $status = $announcement->status;
        $myAuto = MyAuto::where('user_id', $driver_id)->first();
        $DriverLocation = DriverLocation::where('user_id', $driver_id)->first();
        $user = User::where('id',$driver_id)->first();
        $myAutoimage = asset('storage/myAuto') . '/';
        $user_image = asset('storage/user'). '/';
            $fullData =[
                'id' => $myAuto->id,
                'user_id' => $myAuto->user_id,
                'transport_number' => $myAuto->transport_number,
                'transport_model' => $myAuto->transport_model,
                'transport_capacity' => $myAuto->transport_capacity,
                'image' => $myAutoimage . $myAuto->image,
                'longitude'=>$DriverLocation->longitude,
                'latitude'=>$DriverLocation->latitude,
                'updated_at'=>date_format($DriverLocation->updated_at,'Y-m-d H:i:s'),
                'fullname'=>$user->fullname,
                'phone'=>$user->phone,
                'user_image'=>$user->image?$user_image.$user->image:null,
                'status'=>$status
            ];
           
            
        return $fullData;
    }
          
       
}
