<?php

namespace Database\Seeders;

use App\Models\Email;
use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Database\Seeder;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relationship::factory()->count(5)->create();
    }
}
