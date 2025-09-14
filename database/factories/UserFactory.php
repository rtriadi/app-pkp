<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a super admin user.
     */
    public function superAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'super_admin',
        ]);
    }

    /**
     * Create a pejabat penilai (assessor) user.
     */
    public function pejabatPenilai(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'pejabat_penilai',
        ]);
    }

    /**
     * Create an atasan pejabat (supervisor) user.
     */
    public function atasanPejabat(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'atasan_pejabat',
        ]);
    }

    /**
     * Create a pegawai (employee) user.
     */
    public function pegawai(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'pegawai',
        ]);
    }

    /**
     * Create an inactive user.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
