<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\College;
use App\Models\User;

class CollegeTest extends TestCase
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

    public function testCreateCollege()
    {
        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($college: CollegeInput!) {
                    college: create_college(college: $college) {
                        uuid
                        code
                        i18n {
                            locale
                            text
                        }
                    }
                }
            ', [
                'college' => [
                    'code' => 'college',
                    'default_text' => 'College',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'college' => [
                        'code' => 'college',
                        'i18n' => [
                            [
                                'locale' => 'default',
                                'text' => 'College',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(College::where('code', 'college')->exists());
    }

    public function testUpdateCollege()
    {
        $college = factory(College::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $college: CollegeInput!) {
                    college: update_college(uuid: $uuid, college: $college) {
                        uuid
                        code
                        i18n {
                            locale
                            text
                        }
                    }
                }
            ', [
                'uuid' => $college->uuid,
                'college' => [
                    'code' => 'updated_college',
                    'default_text' => 'Updated College',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'college' => [
                        'code' => 'updated_college',
                        'i18n' => [
                            [
                                'locale' => 'default',
                                'text' => 'Updated College',
                            ],
                        ],
                    ],
                ],
            ]);

        $college->refresh();
        $this->assertEquals('updated_college', $college->code);
    }

    public function testDeleteCollege()
    {
        $college = factory(College::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    delete_college(uuid: $uuid)
                }
            ', ['uuid' => $college->uuid]);

        $this->assertFalse($college->exists());
    }
}
