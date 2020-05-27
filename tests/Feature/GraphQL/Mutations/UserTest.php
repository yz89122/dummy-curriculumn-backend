<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function testCreateUser()
    {
        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($user: UserInput!) {
                    user: create_user(user: $user) {
                        username
                        display_name
                    }
                }
            ', ['user' => ['username' => 'user', 'display_name' => 'User']])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'user' => [
                        'username' => 'user',
                        'display_name' => 'User',
                    ],
                ],
            ]);

        $this->assertTrue(User::where('username', 'user')->exists());
    }

    public function testUpdateUser()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $user: UserInput!) {
                    user: update_user(uuid: $uuid, user: $user) {
                        username
                        display_name
                    }
                }
            ', [
                'uuid' => $user->uuid,
                'user' => [
                    'username' => 'updated_user',
                    'display_name' => 'Updated User',
                    'password' => 'UPDATED_PASSWORD'
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'user' => [
                        'username' => 'updated_user',
                        'display_name' => 'Updated User',
                    ],
                ],
            ]);

        $user->refresh();
        $this->assertEquals('updated_user', $user->username);
        $this->assertTrue(Hash::check('UPDATED_PASSWORD', $user->password));
    }

    public function testDeleteUser()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    delete_user(uuid: $uuid)
                }
            ', ['uuid' => $user->uuid])
            ->assertOk();

        $this->assertFalse(User::where('id', $user->id)->exists()); // $user->exists() is not working properly
    }
}
