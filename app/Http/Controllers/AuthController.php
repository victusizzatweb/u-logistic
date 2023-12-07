<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   public function login(LoginRequest $request){
    
 
    $user = User::where('phone', $request->phone)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
       
       
    }

    
    
     if ( $user ) {
        if(Hash::check($request->password, $user->password)){
       
        $accessToken = $user->createToken($request->phone)->plainTextToken;

   $user->update([
    "token"=>$accessToken,
   ]);
   $user->save();

    return response()->json([
        'token' => $accessToken,200
    ]);
}else{
    return response()->json([
        'success' => "Parol xato",
        "error"=>403
    ]);
}
    }else{
        return response()->json([
            'success' => "Not found",
            "error"=>404
        ]);
    }
   }
   public function register(){

   }
   public function logout(){
    
   }
   public function user(Request $request){
    // // return "ishlayapdi";
    return $request->user();
   }
}
