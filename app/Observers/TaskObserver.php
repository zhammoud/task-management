<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Cache::tags('tasks_lists')->flush();
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($task->id);
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($task->id);
        foreach ($task->attachments as $attachment) {
            Storage::disk(config('filesystems.default_attachments'))->delete($attachment);
        }
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        Cache::tags('tasks_lists')->flush();
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        Cache::tags('tasks_lists')->flush();
        Cache::tags('comments_lists')->forget($task->id);
        foreach ($task->attachments as $attachment) {
            Storage::disk(config('filesystems.default_attachments'))->delete($attachment);
        }
    }
}
