<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\College;
use App\Models\Department;

class StudentTest extends TestCase
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

    public function testQueryStudent()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);
        $user = factory(User::class)->create();
        $student = factory(Student::class)->create(['department_id' => $department->id, 'user_id' => $user->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                query ($uuid: String!) {
                    student (uuid: $uuid) {
                        uuid
                        code
                        display_name
                        grade
                        registered_year
                        department {
                            uuid
                        }
                    }
                }
            ', ['uuid' => $student->uuid])
            ->assertJson([
                'data' => [
                    'student' => [
                        'uuid' => $student->uuid,
                        'code' => $student->code,
                        'display_name' => $user->display_name,
                        'grade' => $student->grade,
                        'registered_year' => $student->registered_year,
                        'department' => [
                            'uuid' => $department->uuid,
                        ],
                    ],
                ],
            ]);
    }
}
