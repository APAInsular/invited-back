<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Partner;

class PartnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'Name' => fake()->regexify('[A-Za-z0-9]{400}'),
            'First_Surname' => fake()->regexify('[A-Za-z0-9]{400}'),
            'Second_Surname' => fake()->regexify('[A-Za-z0-9]{400}'),
        ];
    }
}
