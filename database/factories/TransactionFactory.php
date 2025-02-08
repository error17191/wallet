<?php

namespace Database\Factories;

use App\Enums\TransactionType;
use App\Models\Account;
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
            'account_id' => Account::factory(),
            'type' => $this->faker->randomElement([TransactionType::TOP_UP, TransactionType::CHARGE]),
            'amount' => $this->faker->randomFloat(2, 1, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
