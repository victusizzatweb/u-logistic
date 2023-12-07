<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::all();
        return response()->json([
            "data"=>$comments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request,$id)
    {
        dd($request);
        $request->validate([
            'text'=>'required',
        ]);

       dd($request);
        
        if (Auth::check()) 
        {
           
        
        $id = Order::findOrFail($id);
         $comment =  Comment::create([
            'text'=>$request->text,
            'order_id'=>$id,
            'user_id'=>Auth::user()->id
        ]);
        
       }
       return response()->json([
        "success"=>'comment successfuly',
        "data"=> $comment
       ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    
}
