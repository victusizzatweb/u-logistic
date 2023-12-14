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
        if (! $user || ! Hash::check($request->password, $user->password)) {
           return response()->json([
            'phone' => 'The provided credentials are incorrect.',
           ],404);
        }
       
        $accessToken = $user->createToken($request->phone)->plainTextToken;
  
       $user->update([
        "token"=>$accessToken,
       ]);
       $user->save();
    
        return response()->json([
            'token' => $accessToken
        ]);
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
    
    
       $user = $request->user();
    //    dd($user->token);
       // Revoke the user's access tokens
       $user->token->delete();
   
       // Optionally, you can also revoke the user's refresh tokens
       // $user->refreshTokens()->delete();
   
       return response()->json([
           'message' => 'User logged out successfully',
       ]);
   }
   public function user(Request $request){
    // // return "ishlayapdi";
    return $request->user();
   }
  

}
