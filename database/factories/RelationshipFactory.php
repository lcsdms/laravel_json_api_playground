<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\Relationship;
use Illuminate\Database\Eloquent\Factories\Factory;

class RelationshipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Relationship::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->words(3,true),
        ];
    }
}
