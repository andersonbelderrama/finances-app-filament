<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Anderson Belderrama',
            'email' => 'andersonbelderrama@gmail.com',
            'password' => bcrypt('290423'),
        ]);

        User::factory(10)->create();
        Account::factory(30)->create();
        Category::factory(100)->create();
        Transaction::factory(300)->create();
        Budget::factory(50)->create();
        //Goal::factory(10)->create();

    }
}
