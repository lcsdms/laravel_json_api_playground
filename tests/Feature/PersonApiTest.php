<?php

namespace Tests\Feature;

use App\Models\Person;
use Illuminate\Foundation\Testing\WithFaker;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Tests\CreatesApplication;
use Tests\TestCase;

class PersonApiTest extends TestCase
{
    use MakesJsonApiRequests;
    use CreatesApplication;
    use WithFaker;

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

    /**
     * @test
     */
    public function can_get_people_list()
    {
        $this->withoutExceptionHandling();

        $people = Person::factory()->count(3)->create();

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->post('/api/v1/people');

        $response->assertFetchedMany($people);

    }

    /**
     * @test
     */
    public function show_test()
    {
        $person = Person::factory()->create();
        $self = 'http://localhost/api/v1/people/' . $person->getRouteKey();

        $expected = [
            'type' => 'people',
            'id' => (string) $person->getRouteKey(),
            'attributes' => [
                'name' => $person->name ,
                'socialName' => $person->social_name,
                'documentNumber' => $person->document_number,
                'birthDate' => $person->birth_date,
                'createdAt' => $person->created_at->jsonSerialize(),
                'updatedAt' => $person->updated_at->jsonSerialize(),
                'deletedAt' => null
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->get($self);

        $response->assertFetchedOneExact($expected);
    }

    /** @test */
    public function can_create_new_person_and_attach_email()
    {
        $this->markTestIncomplete();
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

        $personId = $response->id();

        $emailAddress = $this->faker->safeEmail;
        $data = [
            'type' => 'emails',
            'attributes' => [
                'address' => $emailAddress,
            ],
            'relationships' => [
                'person' => [
                    'data' => [
                        'type' => 'people',
                        'id' => (string) $personId,
                    ]
                ],
            ],
        ];

        $emailResponse = $this
            ->jsonApi()
            ->expects('emails')
            ->withData($data)
            ->post('/api/v1/emails');

        $emailResponse->assertCreated();

        $person = Person::find($personId);

        $this->assertDatabaseHas('emails', [
            'id' => $emailResponse->id(),
            'address' => $emailResponse->json('data.attributes.address'),
            'entity_id' => $person->entity->id
        ]);
    }
}
