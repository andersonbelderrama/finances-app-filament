<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('type')->comment('expense, revenue, transfer');
            $table->boolean('is_investment')->default(false);
            $table->boolean('has_been_paid')->default(false);
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();

            $table->foreignIdFor(Category::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Account::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
