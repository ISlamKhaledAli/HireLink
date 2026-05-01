<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'job_id' => 'required|exists:job_listings,id',
        ]);

        $comment = Comment::create([
            'content' => $request->content,
            'job_id' => $request->job_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json($comment, 201);
    }

   
    public function index($job_id)
    {
        return Comment::with('user:id,name')
            ->where('job_id', $job_id)
            ->latest()
            ->get();
    }

    
    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Deleted']);
    }
}