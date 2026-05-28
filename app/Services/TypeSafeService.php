<?php

namespace App\Services;

use App\DTOs\TaskData;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TypeSafeService
{
    private array $processedTasks = [];
    private int $taskCounter = 0;

    /**
     * Process a task with strict type checking
     */
    public function processTask(TaskData $task): array
    {
        $this->validateTask($task);
        
        $taskId = ++$this->taskCounter;
        
        $result = [
            'task_id' => $taskId,
            'title' => $task->title,
            'status' => $task->status->value,
            'priority' => $task->priority->value,
            'processed_at' => now()->toISOString(),
            'success' => true,
        ];

        $this->processedTasks[$taskId] = $task;
        
        Log::info('Task processed', [
            'task_id' => $taskId,
            'title' => $task->title,
            'status' => $task->status->value
        ]);

        return $result;
    }

    /**
     * Validate task data with strict types
     */
    private function validateTask(TaskData $task): void
    {
        if (empty(trim($task->title))) {
            throw new \InvalidArgumentException('Task title cannot be empty');
        }

        if ($task->userId <= 0) {
            throw new \InvalidArgumentException('Invalid user ID');
        }

        if ($task->isOverdue()) {
            Log::warning('Task is overdue', ['task' => $task->title]);
        }
    }

    /**
     * Get all processed tasks with type safety
     */
    public function getProcessedTasks(): Collection
    {
        return collect($this->processedTasks);
    }

    /**
     * Update task status with type safety
     */
    public function updateTaskStatus(int $taskId, TaskStatus $newStatus): array
    {
        if (!isset($this->processedTasks[$taskId])) {
            throw new \RuntimeException("Task with ID {$taskId} not found");
        }

        $oldTask = $this->processedTasks[$taskId];
        
        $updatedTask = new TaskData(
            title: $oldTask->title,
            description: $oldTask->description,
            status: $newStatus,
            priority: $oldTask->priority,
            userId: $oldTask->userId,
            dueDate: $oldTask->dueDate,
            metadata: $oldTask->metadata
        );

        $this->processedTasks[$taskId] = $updatedTask;

        Log::info('Task status updated', [
            'task_id' => $taskId,
            'old_status' => $oldTask->status->value,
            'new_status' => $newStatus->value
        ]);

        return [
            'task_id' => $taskId,
            'old_status' => $oldTask->status->label(),
            'new_status' => $newStatus->label(),
            'updated_at' => now()->toISOString()
        ];
    }

    /**
     * Format number with type safety
     */
    public function formatNumber(int|float $number, int $decimals = 2): string
    {
        return number_format((float) $number, $decimals);
    }

    /**
     * Multiply numbers with strict type checking
     */
    public function multiply(int|float $a, int|float $b): float
    {
        return (float) $a * (float) $b;
    }

    /**
     * Filter tasks by status
     */
    public function filterTasksByStatus(TaskStatus $status): Collection
    {
        return collect($this->processedTasks)
            ->filter(fn(TaskData $task) => $task->status === $status);
    }

    /**
     * Get high priority tasks
     */
    public function getHighPriorityTasks(): Collection
    {
        return collect($this->processedTasks)
            ->filter(fn(TaskData $task) => 
                $task->priority === TaskPriority::HIGH || 
                $task->priority === TaskPriority::URGENT
            );
    }

    /**
     * Generate task summary
     */
    public function generateTaskSummary(): array
    {
        $tasks = collect($this->processedTasks);
        
        return [
            'total_tasks' => $tasks->count(),
            'by_status' => TaskStatus::cases()
                ->map(fn($status) => [
                    'status' => $status->label(),
                    'count' => $tasks->filter(fn(TaskData $t) => $t->status === $status)->count()
                ])
                ->toArray(),
            'by_priority' => TaskPriority::cases()
                ->map(fn($priority) => [
                    'priority' => $priority->label(),
                    'count' => $tasks->filter(fn(TaskData $t) => $t->priority === $priority)->count()
                ])
                ->toArray(),
            'overdue_tasks' => $tasks->filter(fn(TaskData $t) => $t->isOverdue())->count(),
            'completed_tasks' => $tasks->filter(fn(TaskData $t) => $t->status->isCompleted())->count(),
        ];
    }
}