<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use App\Models\Role;
use App\Models\SmsCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get_token()
    {
        $url = "https://notify.eskiz.uz/api/auth/login";

        $payload = [
            'email' => 'nationalsoftlab@umail.uz',
            'password' => 'PE02nyieLH3TK0KerHlUxY9fXW16JV0TNZaPD9hZ',
        ];
        $response = Http::post($url, $payload);
        return $response->json();
    }
    public function index()
    {
        $result =  User::all();
        return response()->json([
            "data"=>$result
        ]);
    }
    public function driver()
    {
        $result = User::where('role_id','Haydovchi')->get();
        return response()->json([
            "data"=>$result
        ]);
    }
    public function smsCode(Request $request){
         $request->validate([
            'phone' => 'required',
        ]);
        // $code = rand(10000,99999);
        $code = 55555;
        $phone = $request->phone;
        $user = User::where('phone',$request->phone)->first();
       if($user){
            return response()->json([
                "message"=>"siz royxatdan otgansiz.."
            ],401);
       }else{
            if($phone){
                $url = "https://notify.eskiz.uz/api/message/sms/send";

                try {
                    $payload = [
                        'mobile_phone' => $phone,
                        'message' => "Assalomu alekum ."." sizning code raqamingiz .".$code,
                        'from' => '4546',
                    ];
        
                    $bearerToken = $this->getToken();
        
                    $response = Http::withHeaders([
                        'Authorization' => "Bearer $bearerToken",
                    ])->post($url, $payload);
        
                    if ($response->status() == 401) {
                        $bearerToken = $this->getToken()['data']['token'];
                        file_put_contents('tokenfile.txt', $bearerToken);
                    }
                    $smsCode = new SmsCode;
                    $smsCode->phone = $request->phone;
                    $smsCode->code = $code;
                    $smsCode->save();
                  
                    $responseData = [
                        'id'=>$smsCode->id,
                        'code' => $code,
                        'phone' => $request->phone,
                        'status'=>'pending'
                    ];
                    return response()->json([
                        'message' => 'Sms yuborildi',
                        "data"=>$responseData,
                ],200);
            
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Xato: ' . $e->getMessage()], 400);
                }
              
            }
       
        }
    }
    // public function smscode_status(Request $request){
    //      $request->validate([
    //         'code' => 'required|string',
    //         'phone' => 'required|string',
    //         'status' => 'required|string',
    //     ]);
    //     // $smsCode = SmsCode::where('phone',$request->phone)->first();
    //     $smsCode = SmsCode::where('phone', $request->phone)->orderBy('created_at', 'desc')->first();


    //     if($smsCode){
    //         $smsCode->update([
    //             'status'=>$request->status
    //         ]);
    //         return response()->json([
    //             'message' => 'Update Status Waiting',
    //         ], 200);
    //     }else{
    //         return response()->json([
    //             'error' => 'Phone number not found',
    //         ], 404);
    //     }
    // }
    public function register(Request $request)
    {
        
        // Validate the request data
        $validatedData = $request->validate([
            'fullname' => 'required|string',
            'phone' => 'required|string',
            'role_id' => 'required',
            'password' => 'required',
            'smscode_id'=>'required'
        ]);
        $smsCode = SmsCode::where('id', $request->smscode_id)->first();
        // dd($smsCode);
        if( SmsCode::where('id', $request->smscode_id)->where('phone', $request->phone)->first())
        {
                $user = User::where('phone',$request->phone)->first();
                if($user){
                    return response()->json([
                        'error' => 'Siz royxatdan otgansiz',
                    ], 403);
                }else{
                        $user = User::create([
                            'fullname' => $validatedData['fullname'],
                            'phone' => $validatedData['phone'],
                            'role_id' => $request->role_id,
                            'password' => $request->password,
                        ]);
                        $accessToken = $user->createToken($validatedData['phone'])->plainTextToken;
                        $user->update([
                            'remember_token'=>$accessToken
                          ]);
                          $responseData = [
                            'id'=>$user->id,
                            'token'=>$user->remember_token,
                            'fullname'=>$user->fullname,
                            'role_id'=>$user->role_id,
                            'phone'=>$user->phone,
                            
                        ];
                        $smsCode->update([
                          'user_id'=>$user->id,
                          'status'=>'avtive'
                        ]);
                        
                        return response()->json([
                            'data' => $responseData,
                        ], 200);
                }
        }else{
            return response()->json([
                'error' => 'Sms Code va phone mavjud emas',
            ], 404);
        }
       
    }
    public function forget_password(Request $request)
{
    // Validate the request data
     $request->validate([
        'phone' => 'required'
    ]);

    $code = rand(10000, 99999);
    $user = User::where('phone', $request->phone)->first();

    if ($user) {

        $url = "https://notify.eskiz.uz/api/message/sms/send";

        try {
            $payload = [
                'mobile_phone' => $request->phone,
                'message' => "Sizning kod raqamingiz: " . $code,
                'from' => '4546',
            ];

            $bearerToken = $this->getToken();

            $response = Http::withHeaders([
                'Authorization' => "Bearer $bearerToken",
            ])->post($url, $payload);

            // if ($response->status() == 401) {
            //     $bearerToken = $this->getToken()['data']['token'];
            //     file_put_contents('tokenfile.txt', $bearerToken);
            // }

            // Save the SMS code in the database
            $smsCode = new SmsCode;
            $smsCode->user_id = $user->id;
            $smsCode->phone = $request->phone;
            $smsCode->code = $code;
            $smsCode->save();
            
            $responseData = [
                'user_id' => $user->id,
                'code' => $code,
                'phone' => $request->phone,
                'name' => $user->fullname,
            ];
            return response()->json([
                'message' => 'SMS yuborildi',
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Xato: ' . $e->getMessage()], 400);
        }
    } else {
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }
}

public function forget_password_update(Request $request){
    $request->validate([
     "user_id"=>'required',
     "password"=>'required'
    ]);
    $user = User::where('id',$request->user_id)->first();
    $user->update([
        'password'=>$request->password
    ]);
    
    return response()->json([
        "message"=>"User update password",
        "data"=> $user
    ]);
}

    private function getToken()
    {
        $fileContents = file_get_contents('tokenfile.txt');
        return trim($fileContents);
    }

    public function user_status(Request $request){
        // dd($request);
       $request->validate([
        "user_id" =>"required",
        'phone'=>"required"
       ]);
       
       $user = User::where('id',$request->user_id)->first();
       if($user){
        $user->update([
            "status"=>"active"
        ]);
        $user->save();
                    $responseData = [
                        'user_id' => $user->id,
                        'phone' => $request->phone,
                        'name' => $user->fullname,
                        "status"=>$user->status,
                    ];
        return response()->json([
            "message"=>"Status Waiting",
            "data"=>$responseData
        ],200);
    
       }else{
            return response()->json([
                "message"=>"User not found"
            ],404);
       }
    }
    public function show(string $id)
    {
      if(User::find($id)){
       return User::find($id);
        }else
        return response()->json(['message' => 'User not found'], 404);
        
    }

    public function update(Request $request, $id)
    {
        
        $user = User::find($id);
        // dd($user);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'role_id' => 'required',
            'password' => 'required',
        ]);
 
        if ($user) {
            $user->fullname = $request->input('fullname');
            $user->phone = $request->input('phone');
            $user->status = $request->input('status');
            $user->role_id = $request->input('role_id');
            $user->password = Hash::make($request->input('password'));
            $user->update();
            
            return response()->json(['message' => 'User updated successfully', 'data' => $user], 200);
        }else{
            return response()->json(['message' => 'User not found'], 200);
        }

       
    }
    public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the user.'
        ], 500);
    }
}

}