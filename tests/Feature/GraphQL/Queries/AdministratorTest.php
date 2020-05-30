<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Administrator;

class AdministratorTest extends TestCase
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

    public function testQueryAdministrator()
    {
        $user = factory(User::class)->create();
        $administrator = factory(Administrator::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                query ($uuid: String!) {
                    administrator (uuid: $uuid) {
                        uuid
                        code
                        display_name
                    }
                }
            ', ['uuid' => $administrator->uuid])
            ->assertJson([
                'data' => [
                    'administrator' => [
                        'uuid' => $administrator->uuid,
                        'code' => $administrator->code,
                        'display_name' => $user->display_name,
                    ],
                ],
            ]);
    }
}
