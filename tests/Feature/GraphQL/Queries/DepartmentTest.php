<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\College;
use App\Models\Department;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;

    public function testQueryDepartmentByUuid()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);

        $this
            ->graphQL('
                query ($uuid: String!) {
                    department(uuid: $uuid) {
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
            ', ['uuid' => $department->uuid])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'department' => [
                        'uuid' => $department->uuid,
                        'code' => $department->code,
                        'i18n' => [
                            [
                                'locale' => 'default',
                            ],
                        ],
                        'college' => [
                            'uuid' => $college->uuid,
                        ],
                    ],
                ],
            ]);
    }

    public function testQueryDepartmentByCode()
    {
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);

        $this
            ->graphQL('
                query ($code: String!) {
                    department: department_by_code(code: $code) {
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
            ', ['code' => $department->code])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'department' => [
                        'uuid' => $department->uuid,
                        'code' => $department->code,
                        'i18n' => [
                            [
                                'locale' => 'default',
                            ],
                        ],
                        'college' => [
                            'uuid' => $college->uuid,
                        ],
                    ],
                ],
            ]);
    }
}
