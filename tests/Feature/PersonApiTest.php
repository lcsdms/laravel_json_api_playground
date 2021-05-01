<?php

namespace Tests\Feature;

use App\Models\Person;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Tests\CreatesApplication;
use Tests\TestCase;

class PersonApiTest extends TestCase
{
    use MakesJsonApiRequests;
    use CreatesApplication;

    /** @test */
    public function can_create_new_person()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->make();

        $data = [
            'type' => 'people',
            'attributes' => [
                'name' => $person->name ,
                'socialName' => $person->social_name,
                'documentNumber' => $person->document_number,
                'birthDate' => $person->birth_date
            ]
        ];

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->withData($data)
            ->post('/api/v1/people');

        $response->assertCreated();

        $this->assertDatabaseHas('people', [
            'id' => $response->id()
        ]);
    }

}
