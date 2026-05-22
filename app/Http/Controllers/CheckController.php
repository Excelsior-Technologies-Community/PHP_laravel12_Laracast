<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TypeSafeService;
use App\DTOs\TaskData;
use App\Enums\TaskStatus;

class CheckController extends Controller
{
    public function checkStrictType(Request $request, TypeSafeService $service)
    {
        $taskData = new TaskData(
            title: 'Learn DTO and Enums',
            description: 'Implementing advanced PHP 8.4 features in Laravel 12',
            status: TaskStatus::PROCESSING,
            userId: 1
        );

      
        $result = $service->processTask($taskData);
        
        return response()->json($result);
    }

    public function test(TypeSafeService $service)
    {
        $result1 = $service->formatNumber(10);
        $result2 = $service->multiply(5, 3);

        return $result1 . " | " . $result2;
    }

    public function playground()
    {
        $data = "hello";

        $x = $data . " world";

        $arr = ['missing_key' => 'demo value'];

        return $arr['missing_key'];
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}