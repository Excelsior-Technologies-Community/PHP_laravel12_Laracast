<?php

namespace App\DTOs;

use App\Enums\TaskStatus;
use Illuminate\Http\Request;

readonly class TaskData 
{
    public function __construct(
        public string $title,
        public string $description,
        public TaskStatus $status,
        public ?int $userId = null,
    ) {}


    public static function fromRequest(Request $request): self 
    {
        return new self(
            title: $request->input('title', 'Default Title'),
            description: $request->input('description', 'No description'),
            status: TaskStatus::from($request->input('status', 'pending')),
            userId: $request->user()?->id,
        );
    }
}