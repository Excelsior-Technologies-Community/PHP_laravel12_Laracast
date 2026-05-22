<?php

namespace App\Services;

use App\DTOs\TaskData;
use App\Enums\TaskStatus;

class TypeSafeService
{
    public function processTask(TaskData $data): array
    {
        $title = $data->title;
        $status = $data->status;
        
        if ($status === TaskStatus::COMPLETED) {
            return [
                'success' => false, 
                'message' => 'This task is already completed and cannot be processed again.'
            ];
        }

        return [
            'success' => true,
            'message' => "Task '{$title}' processed successfully.",
            'current_state_label' => $status->label(),
            'data' => [
                'title' => $title,
                'description' => $data->description,
                'status_value' => $status->value, 
            ]
        ];
    }

    public function formatNumber(int $number): string
    {
        return "Number is: " . $number;
    }

    public function multiply(int $a, int $b): int
    {
        return $a * $b;
    }
}