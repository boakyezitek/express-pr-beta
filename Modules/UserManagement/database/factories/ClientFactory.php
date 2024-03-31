<?php

namespace Modules\UserManagement\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\UserManagement\Models\Client::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}

