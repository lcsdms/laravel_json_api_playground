<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Tests\CreatesApplication;
use Tests\TestCase;

class EmailApiTest extends TestCase
{
    use MakesJsonApiRequests;
    use CreatesApplication;
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function create_test()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->create();

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
                        'id' => (string)$person->id,
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

        $this->assertDatabaseHas('emails', [
            'id' => $emailResponse->id(),
            'address' => $emailAddress,
            'entity_id' => $person->entity->id
        ]);


    }


    /**
     * @test
     */
    public function should_throw_error_if_relationship_not_provided()
    {

        Person::factory()->create();

        $emailAddress = $this->faker->safeEmail;
        $data = [
            'type' => 'emails',
            'attributes' => [
                'address' => $emailAddress,
            ]
        ];

        $emailResponse = $this
            ->jsonApi()
            ->expects('emails')
            ->withData($data)
            ->post('/api/v1/emails');

        $emailResponse->assertError(422,[
            'source' => ['pointer' => "/data/relationships/person"],
            'status' => '422'
        ]);
    }

    /**
     * @test
     */
    public function index_test()
    {
        $this->withoutExceptionHandling();
        $person = Person::factory()->create();
        $person->entity->emails()->saveMany(Email::factory()->count(3)->make());
        $emails = $person->entity->emails;

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get('/api/v1/emails');

        $response->assertFetchedMany($emails);

    }

    /**
     * @test show without any includes
     */
    public function show_simple_test()
    {
        $this->withoutExceptionHandling();
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $email = $person->entity->emails()->first();
        $self = 'http://localhost/api/v1/emails/' . $email->getRouteKey();

        $expected = [
            'type' => 'emails',
            'id' => (string)$email->getRouteKey(),
            'attributes' => [
                'address' => $email->address
            ],
            'relationships' => [
                'person' => [
                    'links' => [
                        'self' => "{$self}/relationships/person",
                        'related' => "{$self}/person",
                    ],
                ]
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get($self);

        $response->assertFetchedOne($expected);
    }

    /**
     * @test
     */
    public function show_with_includes_test()
    {
        $this->withoutExceptionHandling();
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $email = $person->entity->emails()->first();
        $self = 'http://localhost/api/v1/emails/' . $email->getRouteKey();

        $expected = [
            'type' => 'emails',
            'id' => (string)$email->getRouteKey(),
            'attributes' => [
                'address' => $email->address
            ],
            'relationships' => [
                'person' => [
                    'links' => [
                        'self' => "{$self}/relationships/person",
                        'related' => "{$self}/person",
                    ],
                ]
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->includePaths("person")
            ->get($self);

        $response->assertFetchedOne($expected);
    }

    /**
     * @test
     * @link https://laraveljsonapi.io/docs/1.0/testing/resources.html#update-testing
     */
    public function update_test()
    {

        $this->withoutExceptionHandling();
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $email = $person->entity->emails()->first();

        $newAddress = $this->faker->safeEmail;
        $newPerson = Person::factory()->create();

        $self = 'http://localhost/api/v1/emails/' . $email->getRouteKey();
        $expected = [
            'type' => 'emails',
            'id' => (string)$email->getRouteKey(),
            'attributes' => [
                'address' => $newAddress
            ],
            'relationships' => [
                'person' => [
                    'links' => [
                        'self' => "{$self}/relationships/person",
                        'related' => "{$self}/person",
                    ],
                ]
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $data = [
            'type' => 'emails',
            'id' => (string) $email->getRouteKey(),
            'attributes' => [
                'address' => $newAddress,
            ],
            'relationships' => [
                'person' => [
                    'data' => [
                        'type' => 'people',
                        'id' => (string) $newPerson->id,
                    ]
                ],
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->includePaths('person')
            ->withData($data)
            ->patch('/api/v1/emails/' . $email->getRouteKey());

        $response->assertFetchedOne($expected);

        $this->assertDatabaseHas('emails', [
            'id' => $email->getKey(),
            'address' => $newAddress,
            'entity_id' => $newPerson->entity->id
        ]);

        /** The existing tag should have been detached. */
        $this->assertDatabaseMissing('emails', [
            'id' => $email->getKey(),
            'address' => $email->address,
            'entity_id' => $person->entity->id
        ]);

    }

    /** @test */
    public function delete_test()
    {
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $email = $person->entity->emails()->first();

        $response = $this
            ->jsonApi()
            ->delete('/api/v1/emails/' . $email->getRouteKey());

        $response->assertNoContent();

        $this->assertDatabaseMissing('emails', [
            'id' => $email->getKey(),
        ]);
    }

}
