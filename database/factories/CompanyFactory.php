<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $foundationDate = $this->faker->dateTimeBetween($startDate = '-50 years', $endDate = '-18 years', $timezone = null);
        return [
            'name' => $this->faker->name,
            'trade_name' => $this->faker->randomElement([$this->faker->name,null]),
            'foundation_date' => $this->faker->randomElement([$foundationDate,null]),
        ];
    }
}
