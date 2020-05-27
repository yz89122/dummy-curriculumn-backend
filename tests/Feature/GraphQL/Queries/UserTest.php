<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;

    protected $administrator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->administrator = factory(User::class)->create(['username' => 'admin', 'display_name' => 'Administrator']);
        $this->administrator->administrator()->create(['code' => '0000']);
    }

    public function testQueryUser()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                query ($uuid: String!) {
                    user (uuid: $uuid) {
                        uuid
                        username
                        display_name
                    }
                }
            ', ['uuid' => $user->uuid])
            ->assertJson([
                'data' => [
                    'user' => [
                        'uuid' => $user->uuid,
                        'username' => $user->username,
                        'display_name' => $user->display_name,
                    ],
                ],
            ]);
    }
}
