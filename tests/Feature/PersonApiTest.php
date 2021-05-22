<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\Person;
use App\Models\Relationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Tests\CreatesApplication;
use Tests\TestCase;

class PersonApiTest extends TestCase
{
    use MakesJsonApiRequests;
    use CreatesApplication;
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function create_test()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->make();
        $relationships = Relationship::factory()->count(2)->create();

        $data = [
            'type' => 'people',
            'attributes' => [
                'name' => $person->name,
                'socialName' => $person->social_name,
                'documentNumber' => $person->document_number,
                'birthDate' => $person->birth_date
            ],
            'relationships' => [
                'relationships' => [
                    'data' => $relationships->map(fn(Relationship $relationship) => [
                        'type' => 'relationships',
                        'id' => (string)$relationship->getRouteKey(),
                    ])->all(),
                ],
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->withData($data)
            ->includePaths('relationships')
            ->post('/api/v1/people');

        $response->assertCreated();

        $personId = $response->id();
        $createdPerson = Person::find($personId);
        $this->assertDatabaseHas('people', [
            'id' => $personId
        ]);

        foreach ($relationships as $relationship){
            $this->assertDatabaseHas('entity_relationship', [
                'entity_id' => $createdPerson->entity->id,
                'relationship_id' => $relationship->id
            ]);
        }

    }

    /**
     * @test
     */
    public function index_test()
    {
        $this->withoutExceptionHandling();

        $people = Person::factory()->count(3)->create();

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->get('/api/v1/people');

        $response->assertFetchedMany($people);

    }

    /**
     * @test
     */
    public function show_test()
    {
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $person->entity->relationships()->attach(Relationship::factory()->count(2)->create());
        $self = 'http://localhost/api/v1/people/' . $person->getRouteKey();

        $expected = [
            'type' => 'people',
            'id' => (string)$person->getRouteKey(),
            'attributes' => [
                'name' => $person->name,
                'socialName' => $person->social_name,
                'documentNumber' => $person->document_number,
                'birthDate' => $person->birth_date,
                'createdAt' => $person->created_at->jsonSerialize(),
                'updatedAt' => $person->updated_at->jsonSerialize(),
                'deletedAt' => null
            ],
            'relationships' => [
                'emails' => [
                    'links' => [
                        'self' => "{$self}/relationships/emails",
                        'related' => "{$self}/emails",
                    ],
                ],
                'relationships' => [
                    'links' => [
                        'self' => "{$self}/relationships/relationships",
                        'related' => "{$self}/relationships",
                    ],
                ]
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('people')
            ->get($self);

        $response->assertFetchedOne($expected);
    }

    /** @test */
    public function show_related_emails()
    {
        $person = Person::factory()->create();
        $person->entity->emails()->saveMany(Email::factory()->count(2)->make());
        $emails = $person->entity->emails;

        $expected = $emails->map(fn(Email $email)=> [
           'type' =>  'emails',
            'id'=> (string) $email->getRouteKey(),
            'attributes' => [
                'address'=> $email->address
            ]
        ]);

        //related resource
        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get('/api/v1/people/'.$person->getRouteKey().'/emails');

        $response->assertFetchedMany($expected);

        //related ids
        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get('/api/v1/people/' . $person->getRouteKey() . '/relationships/emails');

        $response->assertFetchedToMany($emails);
    }


    /** @test */
    public function show_related_relationships()
    {
        $person = Person::factory()->create();
        $person->entity->relationships()->attach(Relationship::factory()->count(2)->create());
        $relationships = $person->entity->relationships;

        $expected = $relationships->map(fn(Relationship $relationship) => [
            'type' => 'relationships',
            'id' => (string)$relationship->getRouteKey(),
            'attributes' => [
                'description' => $relationship->description
            ]
        ]);

        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->get('/api/v1/people/' . $person->getRouteKey() . '/relationships');

        $response->assertFetchedMany($expected);


        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->get('/api/v1/people/' . $person->getRouteKey() . '/relationships/relationships');

        $response->assertFetchedToMany($relationships);
    }

    /** @test */
    public function delete_test()
    {
        $person = Person::factory()->create();
        $person->entity->emails()->save(Email::factory()->make());
        $email = $person->entity->emails()->first();

        $response = $this
            ->jsonApi()
            ->delete('/api/v1/people/'.$person->getRouteKey());

        $response->assertNoContent();

        $this->assertDatabaseMissing('people',[
           'id' => $person->getRouteKey()
        ]);

    }

    /** @test
     * @link https://laraveljsonapi.io/docs/1.0/testing/relationships.html#detach-to-many-testing
     */
    public function detach_emails_relationship()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->create();
        $person->entity->emails()->saveMany(Email::factory()->count(2)->make());
        $emailsToDetach = $person->entity->emails;

        $data = $emailsToDetach->map(fn(Email $email) => [
            'type' => 'emails',
            'id' => (string)$email->getRouteKey(),
        ])->all();

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->withData($data)
            ->delete('/api/v1/people/' . $person->getRouteKey() . '/relationships/emails');

        $response->assertNoContent();

        /** These emails should have been detached. */
        foreach ($emailsToDetach as $email) {
            $this->assertDatabaseMissing('emails', [
                'id' => $email->getKey(),
                'entity_id' => $person->entity->id,
            ]);
        }
    }


    /**
     * @test
     */
    public function detach_relationships_relationship()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->create();
        $person->entity->relationships()->attach(Relationship::factory()->count(2)->create());
        $relationshipsToDetach = $person->entity->relationships;

        $data = $relationshipsToDetach->map(fn(Relationship $relationship) => [
            'type' => 'relationships',
            'id' => (string)$relationship->getRouteKey(),
        ])->all();

        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->withData($data)
            ->delete('/api/v1/people/' . $person->getRouteKey() . '/relationships/relationships');

        $response->assertNoContent();

        foreach ($relationshipsToDetach as $relationship) {
            $this->assertDatabaseMissing('entity_relationship', [
                'id' => $relationship->getKey(),
                'entity_id' => $person->entity->id,
            ]);
        }
    }
}
