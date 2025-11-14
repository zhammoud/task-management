<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($comment->task_id);
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($comment->task_id);
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($comment->task_id);
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($comment->task_id);
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($comment->task_id);
    }
}
