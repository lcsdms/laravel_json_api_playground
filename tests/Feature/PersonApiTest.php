<?php

namespace Tests\Feature;

use App\Models\Email;
use App\Models\Person;
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

        $data = [
            'type' => 'people',
            'attributes' => [
                'name' => $person->name,
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

        $response->assertFetchedOneExact($expected);
    }

    /** @test */
    public function show_related_resources_testing()
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

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get('/api/v1/people/'.$person->getRouteKey().'/emails');

        $response->assertFetchedMany($expected);
    }

    /** @test */
    public function show_to_many_resources_testing()
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

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->get('/api/v1/people/'.$person->getRouteKey().'/relationships/emails');

        $response->assertFetchedToMany($emails);
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

        //using observers to delete related records
        $this->assertDatabaseMissing('emails', [
            'id' => $email->getRouteKey()
        ]);
    }

    /** @test
     * @link https://laraveljsonapi.io/docs/1.0/testing/relationships.html#detach-to-many-testing
     */
    public function detach_to_many_test()
    {
        $this->withoutExceptionHandling();

        $person = Person::factory()->create();
        $person->entity->emails()->saveMany(Email::factory()->count(2)->make());
        $emailsToDetach = $person->entity->emails;

        $data = $emailsToDetach->map(fn(Email $email) => [
            'type' => 'emails',
            'id' => (string) $email->getRouteKey(),
        ])->all();

        $response = $this
            ->jsonApi()
            ->expects('emails')
            ->withData($data)
            ->delete('/api/v1/people/'.$person->getRouteKey().'/relationships/emails');

        $response->assertNoContent();

        /** These emails should have been detached. */
        foreach ($emailsToDetach as $email) {
            $this->assertDatabaseMissing('emails', [
                'id' => $email->getKey(),
                'entity_id' => $person->entity->id,
            ]);
        }
    }
}
