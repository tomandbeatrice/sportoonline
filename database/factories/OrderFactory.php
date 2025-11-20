<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'address' => $this->faker->address(),
            'payment_method' => $this->faker->randomElement(['cash', 'credit_card', 'paypal']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'shipped', 'cancelled']),
            'cart' => json_encode([]),
        ];
    }
}