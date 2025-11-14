<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskIndexRequest $request)
    {
        $tasks = Task::query()->with(['user', 'lastestComments']);
        $validated = $request->validated();
        $cacheKey = 'default';
        if ($validated['status'] ?? null) {
            $cacheKey .= 'status:' . $validated['status'];
            $tasks = $tasks->where('status', TaskStatus::from($validated['status']));
        }
        if ($validated['user_id'] ?? null) {
            $cacheKey .= 'user_id:' . $validated['user_id'];
            $tasks = $tasks->where('user_id', $validated['user_id']);
        }
        $tasks = Cache::tags('tasks_lists')
            ->remember($cacheKey, now()->addDay(), function () use ($cacheKey, $request, $tasks) {
                return $tasks->get();
            });

        return response([
            'result' => 'success',
            'tasks' => TaskResource::collection($tasks ?? []),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $validated = $request->validated();
        $task = Task::query()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => TaskStatus::PENDING,
            'user_id' => Auth::id()
        ]);
        if ($task) {
            $attachments = [];
            try {
                foreach ($validated['attachments'] as $attachment) {
                    $attachmentName = Str::orderedUuid()->toString() . '.' . $attachment->extension();
                    $path = $attachment->storeAs('attachments/' . $task->id, $attachmentName, config('filesystems.default_attachments'));
                    $attachments[] = $path;
                }
            } catch (\Exception) {
                $task->delete();
                foreach ($attachments as $attachment) {
                    Storage::disk(config('filesystems.default_attachments'))->delete($attachment);
                }
                return response()->json([
                    'result' => 'error',
                    'message' => 'Something went wrong, Error 54879'
                ], 500);
            }
            $task->attachments = $attachments;
            $task->save();

            return response()->json([
                'result' => 'success',
                'task' => new TaskResource($task)
            ]);
        }
        return response()->json([
            'result' => 'error',
            'message' => 'Something went wrong, Error 54872'
        ], 500);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $validated = $request->validated();
        $result = $task->update([
            'name' => $validated['name'] ?? $task->name,
            'description' => $validated['description'] ?? $task->description,
            'status' => $validated['status'] ?? $task->status,
        ]);
        if ($result) {
            return response()->json([
                'result' => 'success',
                'task' => new TaskResource($task)
            ]);
        }
        return response()->json([
            'result' => 'error',
            'message' => 'Something went wrong, Error 54873'
        ], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $result = $task->delete();
        if ($result) {
            return response()->json([
                'result' => 'success',
                // return task instance since FE might use it to show messages like: task <title> deleted successfully
                'task' => new TaskResource($task)
            ]);
        }
        return response()->json([
            'result' => 'error',
            'message' => 'Something went wrong, Error 54871'
        ], 500);
    }

}
