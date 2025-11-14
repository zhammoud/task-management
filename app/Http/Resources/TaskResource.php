<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attachments = [];
        foreach (($this->attachments ?? []) as $attachment) {
            $attachments[] = Storage::disk(config('filesystems.default_attachments'))->url($attachment);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'attachments' => $attachments,
            'user' => $this->user->toResource(),
            'comments' => CommentResource::collection($this->whenLoaded('lastestComments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
