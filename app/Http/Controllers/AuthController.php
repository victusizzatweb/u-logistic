<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   
    public function login(LoginRequest $request){
        $user = User::where('phone', $request->phone)->first();
        if($user &&  Hash::check($request->password, $user->password)){
            $responseData = [
                'id'=>$user->id,
                'token'=>$user->remember_token,
                'fullname'=>$user->fullname,
                'role_id'=>$user->role_id,
                'phone'=>$user->phone,
                
            ];
            // dd($responseData);
            return response()->json([
                "data"=>$responseData
            ]);
        }
        elseif (!$user) {
           return response()->json([
            'message' => 'Phone are incorrect.',
           ],401);
        }else{
            return response()->json([
                'message' => 'Password incorrect',
               ],403);
        }
       }
       public function updateToken(Request $request)
        {
        // Get the authenticated user
        $user = Auth::user();

        // Create a new token for the user
        $accessToken = $user->createToken($request->phone)->plainTextToken;

        // Update the user's token in the database
        $user->update([
            'token' => $accessToken,
        ]);

        return response()->json([
            'token' => $accessToken,
        ]);
    }

   public function logout(Request $request)
   {
    
    
    //    Auth::id()->logout();
       
         return response()->json([
           'message' => 'User logged out successfully',
       ]);
   }
   public function user(Request $request){
    // return "ishlayapdi";
    return $request->user();
   }
  

}
