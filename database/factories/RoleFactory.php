<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Role::class;

    public function definition(): array
    {
        // القيمة الافتراضية
        return [
            'name' => 'customer',
            'description' => 'Retail customer',
        ];
    }

    // states لتسهيل إنشاء أدوار محددة
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
            'description' => 'System administrator',
        ]);
    }

    public function teller(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'teller',
            'description' => 'Frontline teller',
        ]);
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'manager',
            'description' => 'Branch manager',
        ]);
    }
}
