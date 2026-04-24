<?php

namespace App\Http\Controllers;

use App\Services\TypeSafeService;

class CheckController extends Controller
{
    // Feature 1: Service Layer test
    public function test(TypeSafeService $service)
    {
        $result1 = $service->formatNumber(10);
        $result2 = $service->multiply(5, 3);

        return $result1 . " | " . $result2;
    }

    // Feature 2: Larastan Playground

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
