<?php

namespace Tests\Feature;

use App\Models\Relationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use LaravelJsonApi\Testing\MakesJsonApiRequests;
use Tests\CreatesApplication;
use Tests\TestCase;

class RelationshipApiTest extends TestCase
{
    use MakesJsonApiRequests;
    use CreatesApplication;
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function create_test()
    {
        $this->withoutExceptionHandling();

        $relationship = Relationship::factory()->make();

        $data = [
            'type' => 'relationships',
            'attributes' => [
                'description' => $relationship->description,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->withData($data)
            ->post('/api/v1/relationships');

        $response->assertCreated();

        $this->assertDatabaseHas('relationships', [
            'id' => $response->id(),
            'description' => $relationship->description
        ]);

    }

    /**
     * @test
     */
    public function index_test()
    {
        $this->withoutExceptionHandling();
        $relationships = Relationship::factory()->count(2)->create();

        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->get('/api/v1/relationships');

        $response->assertFetchedMany($relationships);

    }

    /**
     * @test show without any includes
     */
    public function show_simple_test()
    {
        $this->withoutExceptionHandling();
        $relationship = Relationship::factory()->create();
        $self = 'http://localhost/api/v1/relationships/' . $relationship->getRouteKey();

        $expected = [
            'type' => 'relationships',
            'id' => (string)$relationship->getRouteKey(),
            'attributes' => [
                'description' => $relationship->description
            ]
        ];

        $response = $this
            ->jsonApi()
            ->expects('relationships')
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
        $relationship = Relationship::factory()->create();
        $newDescription = $this->faker()->words(3, true);

        $self = 'http://localhost/api/v1/relationships/' . $relationship->getRouteKey();

        $expected = [
            'type' => 'relationships',
            'id' => (string)$relationship->getRouteKey(),
            'attributes' => [
                'description' => $newDescription
            ],
            'links' => [
                'self' => $self,
            ],
        ];

        $data = [
            'type' => 'relationships',
            'id' => (string)$relationship->getRouteKey(),
            'attributes' => [
                'description' => $newDescription,
            ],
        ];

        $response = $this
            ->jsonApi()
            ->expects('relationships')
            ->withData($data)
            ->patch('/api/v1/relationships/' . $relationship->getRouteKey());

        $response->assertFetchedOne($expected);

        $this->assertDatabaseHas('relationships', [
            'id' => $relationship->getKey(),
            'description' => $newDescription
        ]);

    }

    /** @test */
    public function delete_test()
    {
        $relationship = Relationship::factory()->create();

        $response = $this
            ->jsonApi()
            ->delete('/api/v1/relationships/' . $relationship->getRouteKey());

        $response->assertNoContent();

        $this->assertDatabaseMissing('relationships', [
            'id' => $relationship->getKey(),
        ]);
    }

}
