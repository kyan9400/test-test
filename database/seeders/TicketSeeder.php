<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $managers = User::role(['admin', 'manager'])->get();

        if ($customers->isEmpty()) {
            $customers = Customer::factory(10)->create();
        }

        foreach ($customers->take(15) as $customer) {
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                Ticket::factory()
                    ->for($customer)
                    ->when(
                        $managers->isNotEmpty() && rand(0, 1),
                        fn ($factory) => $factory->assignedTo($managers->random())
                    )
                    ->create([
                        'created_at' => now()->subDays(rand(0, 30)),
                    ]);
            }
        }
    }
}
