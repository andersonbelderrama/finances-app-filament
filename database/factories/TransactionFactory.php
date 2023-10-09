<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'type' => $this->faker->randomElement(['expense', 'revenue', 'transfer']),
            'is_investment' => false,
            'has_been_paid' => $this->faker->boolean(),
            'payment_date' => null,
            'due_date' => null,
            'category_id' => Category::all()->random()->id,
            'user_id' => User::all()->random()->id,
            'account_id' => Account::all()->random()->id,
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
