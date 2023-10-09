<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => $this->faker->word(),
            'bank_branch' => $this->faker->numberBetween(1000, 9999),
            'account_number' =>  $this->faker->numberBetween(1000, 9999),
            'account_name' => $this->faker->name(),
            'account_status' => $this->faker->randomElement([1, 0]),
            'account_type' => $this->faker->randomElement(['current', 'savings', 'investment']),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'user_id' => User::all()->random()->id
        ];
    }
}
