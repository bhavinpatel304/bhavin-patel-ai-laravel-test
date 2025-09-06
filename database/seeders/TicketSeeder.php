<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    public function run()
    {
        // Create 25 tickets using factory
        Ticket::factory()->count(25)->create();
    }
}