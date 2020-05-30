<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CourseTemplate;
use App\Models\User;

class CourseTemplateTest extends TestCase
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

    public function testCreateCourseTemplate()
    {
        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($course_template: CourseTemplateInput!) {
                    course_template {
                        create(course_template: $course_template) {
                            uuid
                            code
                            i18n {
                                locale
                                text
                            }
                        }
                    }
                }
            ', [
                'course_template' => [
                    'code' => 'course_template',
                    'default_text' => 'CourseTemplate',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'course_template' => [
                        'create' => [
                            'code' => 'course_template',
                            'i18n' => [
                                [
                                    'locale' => 'default',
                                    'text' => 'CourseTemplate',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(CourseTemplate::where('code', 'course_template')->exists());
    }

    public function testUpdateCourseTemplate()
    {
        $course_template = factory(CourseTemplate::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $course_template: CourseTemplateInput!) {
                    course_template {
                        update(uuid: $uuid, course_template: $course_template) {
                            uuid
                            code
                            i18n {
                                locale
                                text
                            }
                        }
                    }
                }
            ', [
                'uuid' => $course_template->uuid,
                'course_template' => [
                    'code' => 'updated_course_template',
                    'default_text' => 'Updated CourseTemplate',
                    'i18n' => [],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'course_template' => [
                        'update' => [
                            'code' => 'updated_course_template',
                            'i18n' => [
                                [
                                    'locale' => 'default',
                                    'text' => 'Updated CourseTemplate',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $course_template->refresh();
        $this->assertEquals('updated_course_template', $course_template->code);
    }

    public function testDeleteCourseTemplate()
    {
        $course_template = factory(CourseTemplate::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    course_template {
                        delete(uuid: $uuid)
                    }
                }
            ', ['uuid' => $course_template->uuid]);

        $this->assertTrue($course_template->refresh()->trashed());
    }
}
