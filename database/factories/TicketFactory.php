<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;


class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
       
        $categories = ['technical', 'billing', 'general_inquiry', 'bug_report', 'feature_request'];

        return [
            'subject' => $this->faker->sentence(5),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(TicketStatus::cases()),
            'note' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement($categories),
            'explanation' => $this->faker->paragraph(),
            'confidence' => $this->faker->randomFloat(2, 0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}