<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use App\Models\Student;

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

    public function testCreateStudent()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);
        $user = factory(User::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($student: StudentInput!) {
                    student {
                        create(student: $student) {
                            uuid
                            code
                            display_name
                            registered_year
                            grade
                            department {
                                uuid
                            }
                        }
                    }
                }
            ', [
                'student' => [
                    'code' => 'student',
                    'user_uuid' => $user->uuid,
                    'registered_year' => $year = now()->year,
                    'department_uuid' => $department->uuid,
                    'grade' => 'Freshman',
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'student' => [
                        'create' => [
                            'code' => 'student',
                            'display_name' => $user->display_name,
                            'registered_year' => $year,
                            'grade' => 'Freshman',
                            'department' => [
                                'uuid' => $department->uuid,
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(Student::where('code', 'student')->exists());
    }

    public function testUpdateStudent()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);
        $new_department = factory(Department::class)->create(['college_id' => $college->id]);
        $user = factory(User::class)->create();
        $new_user = factory(User::class)->create();
        $student = factory(Student::class)->create(['user_id' => $user->id, 'department_id' => $department->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $student: StudentInput!) {
                    student {
                        update(uuid: $uuid, student: $student) {
                            uuid
                            code
                            display_name
                            registered_year
                            grade
                            department {
                                uuid
                            }
                        }
                    }
                }
            ', [
                'uuid' => $student->uuid,
                'student' => [
                    'code' => 'student',
                    'user_uuid' => $new_user->uuid,
                    'registered_year' => $year = now()->year - 10,
                    'department_uuid' => $new_department->uuid,
                    'grade' => $grade = 'Graduate',
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'student' => [
                        'update' => [
                            'code' => 'student',
                            'display_name' => $new_user->display_name,
                            'registered_year' => $year,
                            'grade' => $grade,
                            'department' => [
                                'uuid' => $new_department->uuid,
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function testDeleteStudent()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);
        $user = factory(User::class)->create();
        $student = factory(Student::class)->create(['user_id' => $user->id, 'department_id' => $department->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    student {
                        delete(uuid: $uuid)
                    }
                }
            ', [
                'uuid' => $student->uuid,
            ])
            ->assertOk();

        $this->assertTrue($student->refresh()->trashed());
    }
}
