<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Mail\CommentAddedNotification;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Task $task)
    {
        $comments = Comment::query();
        $comments = $comments->where('task_id', $task->id);
        $comments->with(['user', 'task']);

        $comments = Cache::tags('comments_lists')
            ->remember($task->id, now()->addDay(), function () use ($comments) {
                return $comments->get();
            });
        return response()->json([
            'result' => 'success',
            'comments' => CommentResource::collection($comments)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentStoreRequest $request, Task $task)
    {
        $body = $request->validated('body');
        $user = $request->user();
        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'body' => $body,
        ]);
        if (!$comment) {
            return response([
                'result' => 'error',
                'message' => 'Something went wrong, Error 20222'
            ], 500);
        }
        $comment->load(['user', 'task']);
        Mail::queue(new CommentAddedNotification($comment));
        return response([
            'result' => 'success',
            'comment' => new CommentResource($comment)
        ]);
    }
}
