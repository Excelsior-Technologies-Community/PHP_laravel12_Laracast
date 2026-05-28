<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TypeSafeService;
use App\DTOs\TaskData;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;

class TestTypeSafety extends Command
{
    protected $signature = 'test:type-safety';
    protected $description = 'Test type safety features with Larastan';

    public function handle(TypeSafeService $service): int
    {
        $this->info('🧪 Testing Type Safety Features...');
        
        $tasks = [
            new TaskData(
                title: 'Console Task 1',
                description: 'Testing from CLI',
                status: TaskStatus::PENDING,
                priority: TaskPriority::MEDIUM,
                userId: 1
            ),
            new TaskData(
                title: 'Console Task 2',
                description: 'Another test',
                status: TaskStatus::PROCESSING,
                priority: TaskPriority::HIGH,
                userId: 2,
                dueDate: now()->addDays(2)
            ),
        ];

        foreach ($tasks as $task) {
            $result = $service->processTask($task);
            $this->info("✅ Processed: {$result['title']} (ID: {$result['task_id']})");
        }

        $summary = $service->generateTaskSummary();
        $this->table(
            ['Metric', 'Value'],
            collect($summary)->map(fn($value, $key) => [$key, is_array($value) ? json_encode($value) : $value])->toArray()
        );

        $this->info('✨ Type safety test completed successfully!');
        
        return self::SUCCESS;
    }
}