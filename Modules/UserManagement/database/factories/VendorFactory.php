<?php

namespace Modules\UserManagement\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\UserManagement\Models\Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed> The default state of the model.
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->freeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'zipcode' => $this->faker->postcode(),
        ];
    }
}

