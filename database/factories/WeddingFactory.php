<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Wedding;

class WeddingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wedding::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'Ceremony_Start_Time' => fake()->time(),
            'Lunch_Start_Time' => fake()->time(),
            'Dinner_Start_Time' => fake()->time(),
            'Party_Start_Time' => fake()->time(),
            'Party_Finish_Time' => fake()->time(),
            'Dress_Code' => fake()->randomElement(["etiqueta",""]),
            'Wedding_Date' => fake()->date(),
            'Music' => fake()->word(),
        ];
    }
}
