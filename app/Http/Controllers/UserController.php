<?php

namespace App\Http\Controllers;

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
        return User::all();
    }

    public function register(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'fullname' => 'required|string',
            'phone' => 'required|string',
            'role_id' => 'required',
            'password' => 'required',
            'password2' => 'required',
        ]);
        $role = Role::where("id",$request->role_id)->first();
        $code = rand(10000,99999);
        $user = User::where('phone',$request->phone)->first();
        if($request->password == $request->password2){
           $user = User::create([
                'fullname' => $validatedData['fullname'],
                'phone' => $validatedData['phone'],
                'role_id' => $role->name,
                'password' => $request->password,
            ]);
            if($user){
                $url = "https://notify.eskiz.uz/api/message/sms/send";

                try {
                    $payload = [
                        'mobile_phone' => $request->phone,
                        'message' => "Assalomu alekum .".$code,
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
                    $smsCode->user_id = $user->id;
                    $smsCode->phone = $request->phone;
                    $smsCode->code = $code;
                    $smsCode->save();
                    return response()->json([
                        'message' => 'Sms yuborildi',
                        "data"=>$smsCode,
                        "data2"=>$user
                ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Xato: ' . $e->getMessage()], 400);
                }
            }else{
                    return response([
                        'message'=>'tel raqam xato'
                    ]);
                }
        } else{
            return response([
                'message'=>'parol xato'
            ]);
        }
        
        
       
    }
    private function getToken()
    {
        $fileContents = file_get_contents('tokenfile.txt');
        return trim($fileContents);
    }

    public function smsCode(Request $request){
        // $request->validate([
        //     'phone'=>'required',
        //     'code'=>'required',
        // ]);
        // $time = time();
        // $user =  User::where('phone',$request->phone)->first();
        // if($user){
        //     if($time - $user->code_time_create <= 180){
        //         if($user->code == $request->code){
        //             $user->status = 'active';
        //             $user->save();
        //             return response([
        //                 'succes'=>true,
        //                 'message'=>'user active',
        //                 'user'=>['phone'=>$user->phone,'name'=>$user->fullname]
        //             ]);
        //         }else{
        //             return ' sms code xato';
        //         }
        //     }else{
        //         return response([
        //             'succes'=> false,
        //             'message'=>'Code entry timed out'
        //         ]);
        //     }
        // }else{
        //     return response([
        //         'succes'=> false,
        //         'message'=>'You are not registered'
        //     ]);
        // }
    }
    /**
     * Display the specified resource.
     */
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




    /**
     * Remove the specified resource from storage.
     */
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