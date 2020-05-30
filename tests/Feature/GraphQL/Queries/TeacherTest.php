<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;

class TeacherTest extends TestCase
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

    public function testQueryTeacher()
    {
        $user = factory(User::class)->create();
        $teacher = factory(Teacher::class)->create(['user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                query ($uuid: String!) {
                    teacher (uuid: $uuid) {
                        uuid
                        code
                        display_name
                    }
                }
            ', ['uuid' => $teacher->uuid])
            ->assertJson([
                'data' => [
                    'teacher' => [
                        'uuid' => $teacher->uuid,
                        'code' => $teacher->code,
                        'display_name' => $user->display_name,
                    ],
                ],
            ]);
    }
}
