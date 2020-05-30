<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\College;
use App\Models\Department;
use App\Models\User;

class DepartmentTest extends TestCase
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

    public function testCreateDepartment()
    {
        $college = factory(College::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($department: DepartmentInput!) {
                    department: create_department(department: $department) {
                        uuid
                        code
                        i18n {
                            locale
                            text
                        }
                        college {
                            uuid
                        }
                    }
                }
            ', [
                'department' => [
                    'code' => 'department',
                    'college_uuid' => $college->uuid,
                    'default_text' => 'Department',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'department' => [
                        'code' => 'department',
                        'college' => [
                            'uuid' => $college->uuid,
                        ],
                        'i18n' => [
                            [
                                'locale' => 'default',
                                'text' => 'Department',
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(Department::where('code', 'department')->exists());
    }

    public function testUpdateDepartment()
    {
        $college = factory(College::class)->create();
        $new_college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $department: DepartmentInput!) {
                    department: update_department(uuid: $uuid, department: $department) {
                        uuid
                        code
                        i18n {
                            locale
                            text
                        }
                        college {
                            uuid
                        }
                    }
                }
            ', [
                'uuid' => $department->uuid,
                'department' => [
                    'code' => 'updated_department',
                    'college_uuid' => $new_college->uuid,
                    'default_text' => 'Updated Department',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'department' => [
                        'code' => 'updated_department',
                        'college' => [
                            'uuid' => $new_college->uuid,
                        ],
                        'i18n' => [
                            [
                                'locale' => 'default',
                                'text' => 'Updated Department',
                            ],
                        ],
                    ],
                ],
            ]);

        $department->refresh();
        $this->assertEquals('updated_department', $department->code);
    }

    public function testDeleteDepartment()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    delete_department(uuid: $uuid)
                }
            ', ['uuid' => $department->uuid]);

        $this->assertTrue($department->refresh()->trashed());
    }
}
