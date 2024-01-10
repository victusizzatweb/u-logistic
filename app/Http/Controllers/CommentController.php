<?php

// app/Http/Controllers/CommentController.php

namespace App\Http\Controllers;

use App\Events\CommentPosted;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $comment = Comment::create([
            'announcement_id' => $request->announcement_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        broadcast(new CommentPosted($comment));

        return response()->json($comment);
    }

    public function index($announcementId)
    {
        $comments = Comment::where('announcement_id', $announcementId)->get();

        return response()->json($comments);
    }
}
