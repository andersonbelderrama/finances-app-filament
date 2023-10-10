<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        $category = Category::where('user_id', $user->id)->inRandomOrder()->first();

        return [
            'name' => 'Orçamento ' . $this->faker->word(),
            'description' => $this->faker->sentence(2),
            'budget_limit' => $this->faker->randomNumber(2),
            'budget_used' => $this->faker->randomNumber(2),
            'period' => $this->faker->randomElement(['monthly', 'quarterly', 'semiannually', 'yearly']),

            'category_id' => $category->id,
            'user_id' => $user->id,
        ];
    }
}
