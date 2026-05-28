<?php

namespace App\DTOs;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Support\Carbon;

class TaskData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly TaskStatus $status,
        public readonly TaskPriority $priority,
        public readonly int $userId,
        public readonly ?Carbon $dueDate = null,
        public readonly array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            status: TaskStatus::from($data['status']),
            priority: TaskPriority::from($data['priority']),
            userId: $data['user_id'],
            dueDate: isset($data['due_date']) ? Carbon::parse($data['due_date']) : null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'priority' => $this->priority->value,
            'priority_label' => $this->priority->label(),
            'priority_color' => $this->priority->color(),
            'user_id' => $this->userId,
            'due_date' => $this->dueDate?->toISOString(),
            'metadata' => $this->metadata,
            'is_completed' => $this->status->isCompleted(),
        ];
    }

    public function isOverdue(): bool
    {
        if (!$this->dueDate) {
            return false;
        }

        return $this->dueDate->isPast() && !$this->status->isCompleted();
    }
}