<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Guest;
use App\Models\Weeding;

class GuestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Guest::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'Name' => fake()->regexify('[A-Za-z0-9]{400}'),
            'First_Surname' => fake()->regexify('[A-Za-z0-9]{400}'),
            'Second_Surname' => fake()->regexify('[A-Za-z0-9]{400}'),
            'Extra_Information' => fake()->text(),
            'Allergy' => fake()->text(),
            'Feeding' => fake()->regexify('[A-Za-z0-9]{400}'),
            'weeding_id' => Weeding::factory(),
        ];
    }
}
