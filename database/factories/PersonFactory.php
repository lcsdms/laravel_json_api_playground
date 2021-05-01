<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $birthDate = $this->faker->dateTimeBetween($startDate = '-50 years', $endDate = '-18 years', $timezone = null);
        return [
            'name' => $this->faker->name,
            'social_name' => $this->faker->randomElement([$this->faker->name,null]),
            'birth_date' => $this->faker->randomElement([$birthDate,null]),
            'document_number' => $this->faker->randomElement([(string) $this->faker->randomNumber(), null])
        ];
    }
}
