<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Person::factory()->count(20)->create();
        Person::factory()->count(20)
            ->create()->each(function($person){
               $person->entity->emails()->saveMany(Email::factory()->count(random_int(1,3))->make());
            });
    }
}
