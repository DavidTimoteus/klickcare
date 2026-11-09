<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'noId' => (string) fake()->unique()->numerify('214176####'),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'gender' => fake()->randomElement(['Laki-Laki', 'Perempuan']),
            'birthDate' => fake()->date('Y-m-d', '-20 years'),
            'address' => fake()->address(),
            'phoneNumber' => fake()->phoneNumber(),
            'role' => User::ROLE_STUDENT,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STAFF,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STUDENT,
        ]);
    }
}
