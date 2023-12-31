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
        $user = User::all()->random();
        $category = Category::where('user_id', $user->id)->inRandomOrder()->first();
        $account = Account::where('user_id', $user->id)->inRandomOrder()->first();

        return [
            'name' => 'Transação ' . $this->faker->word() ,
            'description' => $this->faker->sentence(2),
            'amount' => $this->faker->randomFloat(2, 1, 100),
            'type' => $this->faker->randomElement(['expense', 'revenue', 'transfer']),
            'is_investment' => false,
            'has_been_paid' => $this->faker->boolean(),
            'payment_date' => null,
            'due_date' => null,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'account_id' => $account->id,
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
