<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => User::TYPE_USER,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$yHOVJzIYR5NRsj1JVFrKIuJ8X4JZHlW7Y7QAgRPpnd4MEp9uglwHK', // 123456
            'mobile' => '+989' . random_int(1111, 9999) . random_int(11111, 99999),
            'avatar' => null,
            'website' => $this->faker->url,
            'verify_code' => null,
            'verified_at' => now(),
        ];
    }

    public function admin()
    {
        return $this->state([
            'type' => User::TYPE_ADMIN
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified_at' => null,
            ];
        });
    }
}
