<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $status = fake()->randomElement(TicketStatus::cases());

        return [
            'customer_id' => Customer::factory(),
            'assigned_user_id' => fake()->boolean(30) ? User::factory() : null,
            'subject' => fake()->sentence(4),
            'text' => fake()->paragraphs(2, true),
            'status' => $status,
            'answered_at' => $status === TicketStatus::DONE ? fake()->dateTimeBetween('-1 week') : null,
        ];
    }

    public function new(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::NEW,
            'answered_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::IN_PROGRESS,
            'answered_at' => null,
        ]);
    }

    public function done(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::DONE,
            'answered_at' => now(),
        ]);
    }

    public function assignedTo(User $user): static
    {
        return $this->state(fn () => [
            'assigned_user_id' => $user->id,
        ]);
    }
}
