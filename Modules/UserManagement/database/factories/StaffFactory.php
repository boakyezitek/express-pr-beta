<?php

namespace Modules\UserManagement\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\UserManagement\Enum\UserType;

class StaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\UserManagement\Models\Staff::class;

    /**
     * Define the model's default state.
     * @return array<string, mixed> A set of attributes that should be generated for each instance.
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->freeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'staff_type' => UserType::MANAGER,
            'is_visible_on_website' => true,
        ];
    }
}

