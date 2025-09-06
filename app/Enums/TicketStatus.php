<?php

namespace App\Enums;

enum TicketStatus : string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public static function select(): array
    {
        return array_map(function ($case) {
            return ['name' => $case->name, 'value' => $case->value];
        }, self::cases());
    }
    
    public static function values(): array
    {
        return array_map(fn(self $status) => $status->value, self::cases());
    }
}
