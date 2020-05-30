<?php

namespace Tests\Feature\GraphQL\Mutations;

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

    public function testCreateAdministrator()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($administrator: AdministratorInput!) {
                    administrator: create_administrator(administrator: $administrator) {
                        uuid
                        code
                        display_name
                    }
                }
            ', [
                'administrator' => [
                    'code' => 'administrator',
                    'user_uuid' => $user->uuid,
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'administrator' => [
                        'code' => 'administrator',
                        'display_name' => $user->display_name,
                    ],
                ],
            ]);

        $this->assertTrue(Administrator::where('code', 'administrator')->exists());
    }

    public function testUpdateAdministrator()
    {
        $user = factory(User::class)->create();
        $new_user = factory(User::class)->create();
        $administrator = factory(Administrator::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $administrator: AdministratorInput!) {
                    administrator: update_administrator(uuid: $uuid, administrator: $administrator) {
                        uuid
                        code
                        display_name
                    }
                }
            ', [
                'uuid' => $administrator->uuid,
                'administrator' => [
                    'code' => 'updated_administrator',
                    'user_uuid' => $new_user->uuid,
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'administrator' => [
                        'code' => 'updated_administrator',
                        'display_name' => $new_user->display_name,
                    ],
                ],
            ]);
    }

    public function testDeleteAdministrator()
    {
        $user = factory(User::class)->create();
        $administrator = factory(Administrator::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    delete_administrator(uuid: $uuid)
                }
            ', [
                'uuid' => $administrator->uuid,
            ])
            ->assertOk();

        $this->assertTrue($administrator->refresh()->trashed());
    }
}
