<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => $this->generateE164Phone(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    private function generateE164Phone(): string
    {
        $countryCodes = ['+1', '+44', '+49', '+33', '+7', '+380'];
        $countryCode = fake()->randomElement($countryCodes);
        $number = fake()->numerify('##########');

        return $countryCode . $number;
    }
}
