<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TypeSafeService;
use App\DTOs\TaskData;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CheckController extends Controller
{
    /**
     * Demonstrate strict type checking with DTO and Enums
     */
    public function checkStrictType(Request $request, TypeSafeService $service): JsonResponse
    {
        $taskData = new TaskData(
            title: 'Learn DTO and Enums',
            description: 'Implementing advanced PHP 8.4 features in Laravel 12',
            status: TaskStatus::PROCESSING,
            priority: TaskPriority::HIGH,
            userId: 1,
            metadata: ['source' => 'larastan', 'version' => '2.0']
        );

        $result = $service->processTask($taskData);
        
        // Process multiple tasks
        $tasks = [
            new TaskData(
                title: 'Setup Larastan',
                description: 'Configure PHPStan for Laravel',
                status: TaskStatus::COMPLETED,
                priority: TaskPriority::HIGH,
                userId: 1
            ),
            new TaskData(
                title: 'Write Documentation',
                description: 'Create comprehensive documentation',
                status: TaskStatus::PENDING,
                priority: TaskPriority::MEDIUM,
                userId: 1,
                dueDate: now()->addDays(3)
            ),
        ];

        foreach ($tasks as $task) {
            $service->processTask($task);
        }

        $summary = $service->generateTaskSummary();
        $highPriorityTasks = $service->getHighPriorityTasks();

        return response()->json([
            'processed_task' => $result,
            'summary' => $summary,
            'high_priority_tasks' => $highPriorityTasks->values()->toArray(),
            'service_status' => 'active',
            'php_version' => PHP_VERSION,
        ]);
    }

    /**
     * Test service layer functionality
     */
    public function test(TypeSafeService $service): JsonResponse
    {
        $formattedNumber = $service->formatNumber(1234.5678, 2);
        $multiplication = $service->multiply(5, 3);
        
        // Process a test task
        $testTask = new TaskData(
            title: 'Test Task',
            description: 'Testing service layer',
            status: TaskStatus::PENDING,
            priority: TaskPriority::LOW,
            userId: 999,
            dueDate: now()->addDays(7)
        );
        
        $processed = $service->processTask($testTask);
        
        // Update task status
        $updated = $service->updateTaskStatus(1, TaskStatus::COMPLETED);
        
        return response()->json([
            'formatted_number' => $formattedNumber,
            'multiplication_result' => $multiplication,
            'processed_task' => $processed,
            'status_update' => $updated,
            'total_processed_tasks' => $service->getProcessedTasks()->count(),
        ]);
    }

    /**
     * Larastan playground - demonstrate type safety
     */
    public function playground(): JsonResponse
    {
        // This would cause Larastan error if uncommented:
        // $wrongType = strtoupper(123); // Integer passed to strtoupper
        
        $correctType = strtoupper("hello world");
        
        // Demonstrate enum usage
        $status = TaskStatus::PROCESSING;
        $priority = TaskPriority::URGENT;
        
        // Demonstrate DTO
        $task = new TaskData(
            title: "Playground Task",
            description: "Testing Larastan features",
            status: $status,
            priority: $priority,
            userId: 1,
            dueDate: now()
        );
        
        return response()->json([
            'message' => 'Larastan Playground - Type Safe!',
            'string_operation' => $correctType,
            'enum_demo' => [
                'status' => $status->label(),
                'priority' => $priority->label(),
                'priority_color' => $priority->color(),
            ],
            'dto_demo' => $task->toArray(),
            'larastan_status' => 'configured_at_level_5',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    }

    /**
     * Dashboard view
     */
    public function dashboard(): View
    {
        return view('dashboard');
    }

    /**
     * Advanced type checking demo
     */
    public function advancedDemo(TypeSafeService $service): JsonResponse
    {
        $tasks = [
            new TaskData(
                title: 'Critical Bug Fix',
                description: 'Fix production issue',
                status: TaskStatus::PROCESSING,
                priority: TaskPriority::URGENT,
                userId: 2,
                dueDate: now()->addHours(2)
            ),
            new TaskData(
                title: 'Code Review',
                description: 'Review pull requests',
                status: TaskStatus::PENDING,
                priority: TaskPriority::HIGH,
                userId: 3,
                dueDate: now()->addDay()
            ),
            new TaskData(
                title: 'Write Tests',
                description: 'Add unit tests',
                status: TaskStatus::COMPLETED,
                priority: TaskPriority::MEDIUM,
                userId: 1
            ),
        ];

        foreach ($tasks as $task) {
            $service->processTask($task);
        }

        $pendingTasks = $service->filterTasksByStatus(TaskStatus::PENDING);
        $highPriority = $service->getHighPriorityTasks();
        $summary = $service->generateTaskSummary();

        return response()->json([
            'total_tasks' => count($tasks),
            'pending_count' => $pendingTasks->count(),
            'high_priority_count' => $highPriority->count(),
            'summary' => $summary,
            'overdue_warning' => $summary['overdue_tasks'] > 0 ? 'Some tasks are overdue!' : 'All tasks on track',
        ]);
    }
}