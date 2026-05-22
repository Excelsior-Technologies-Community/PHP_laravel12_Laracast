<?php

namespace App\Enums;


enum TaskStatus: string 
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

   
    public function label(): string 
    {
        return match($this) {
            self::PENDING => '⏳ Task is Pending',
            self::PROCESSING => '⚙️ Task is Processing',
            self::COMPLETED => '✅ Completed Successfully',
            self::FAILED => '❌ Task Failed',
        };
    }
}