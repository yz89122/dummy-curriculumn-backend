<?php

namespace Tests\Feature\GraphQL\Queries;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\College;

class CollegeTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;

    public function testQueryCollegeByUuid()
    {
        $college = factory(College::class)->create();
        $this->graphQL('
            query ($uuid: String!) {
                college(uuid: $uuid) {
                    uuid
                    code
                    i18n {
                        locale
                        text
                    }
                }
            }
        ', ['uuid' => $college->uuid])->assertJson([
            'data' => [
                'college' => [
                    'uuid' => $college->uuid,
                    'code' => $college->code,
                    'i18n' => [
                        [
                            'locale' => 'default',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testQueryCollegeByCode()
    {
        $college = factory(College::class)->create();
        $this->graphQL('
            query ($code: String!) {
                college: college_by_code(code: $code) {
                    uuid
                    code
                    i18n {
                        locale
                        text
                    }
                }
            }
        ', ['code' => $college->code])->assertJson([
            'data' => [
                'college' => [
                    'uuid' => $college->uuid,
                    'code' => $college->code,
                    'i18n' => [
                        [
                            'locale' => 'default',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
