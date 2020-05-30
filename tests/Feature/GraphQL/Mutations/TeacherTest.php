<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;

class TeacherTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->administrator = factory(User::class)->create(['username' => 'admin', 'display_name' => 'Administrator']);
        $this->administrator->administrator()->create(['code' => '0000']);
    }

    public function testCreateTeacher()
    {
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($teacher: TeacherInput!) {
                    teacher {
                        create(teacher: $teacher) {
                            uuid
                            code
                            display_name
                        }
                    }
                }
            ', [
                'teacher' => [
                    'code' => 'teacher',
                    'user_uuid' => $user->uuid,
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'teacher' => [
                        'create' => [
                            'code' => 'teacher',
                            'display_name' => $user->display_name,
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(Teacher::where('code', 'teacher')->exists());
    }

    public function testUpdateTeacher()
    {
        $user = factory(User::class)->create();
        $new_user = factory(User::class)->create();
        $teacher = factory(Teacher::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $teacher: TeacherInput!) {
                    teacher {
                        update(uuid: $uuid, teacher: $teacher) {
                            uuid
                            code
                            display_name
                        }
                    }
                }
            ', [
                'uuid' => $teacher->uuid,
                'teacher' => [
                    'code' => 'updated_teacher',
                    'user_uuid' => $new_user->uuid,
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'teacher' => [
                        'update' => [
                            'code' => 'updated_teacher',
                            'display_name' => $new_user->display_name,
                        ],
                    ],
                ],
            ]);
    }

    public function testDeleteTeacher()
    {
        $user = factory(User::class)->create();
        $teacher = factory(Teacher::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    teacher {
                        delete(uuid: $uuid)
                    }
                }
            ', [
                'uuid' => $teacher->uuid,
            ])
            ->assertOk();

        $this->assertTrue($teacher->refresh()->trashed());
    }
}
