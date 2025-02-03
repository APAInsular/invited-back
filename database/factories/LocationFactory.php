<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Location;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'Population' => fake()->regexify('[A-Za-z0-9]{400}'),
            'Postal_Code' => fake()->numberBetween(-10000, 10000),
            'City' => fake()->regexify('[A-Za-z0-9]{400}'),
            'Country' => fake()->regexify('[A-Za-z0-9]{400}'),
        ];
    }
}
