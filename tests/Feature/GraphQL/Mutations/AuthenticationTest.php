<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthenticationTest extends TestCase
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

    public function testAuthenticate()
    {
        $this
            ->graphQL('
                mutation ($credentials: UserCredentialsInput!) {
                    authorization {
                        authenticate(credentials: $credentials) {
                            access_token
                            expires_in
                        }
                    }
                }
            ', [
                'credentials' => [
                    'username' => $this->administrator->username,
                    'password' => 'password',
                ],
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'authorization' => [
                        'authenticate' => [
                            'access_token',
                            'expires_in',
                        ],
                    ],
                ],
            ]);
    }

    public function testRefreshAuthorization()
    {
        $this
            ->actingAs($this->administrator)
            ->json(
                'POST',
                '/api/graphql',
                [
                    'query' => '
                        mutation {
                            authorization {
                                refresh {
                                    access_token
                                    expires_in
                                }
                            }
                        }
                    ',
                ],
                [
                    'Authorization' => 'bearer '.app('auth')->attempt([
                        'username' => $this->administrator->username,
                        'password' => 'password',
                    ]),
                ]
            )
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'authorization' => [
                        'refresh' => [
                            'access_token',
                            'expires_in',
                        ],
                    ],
                ],
            ]);
    }
}
