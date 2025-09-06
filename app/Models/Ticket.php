<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;


class Ticket extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'tickets';
    public $incrementing = false;
    protected $keyType = 'string';

    const CATEGORIES = [
        'manual_bug_fix',
        'manual_request',
        'manual_support',
    ];

    protected $fillable = [
        'subject',
        'body',
        'status',
        'note',
        'category',
        'explanation',
        'confidence',
        'category_changed',
        'is_classified',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
    ];
}
