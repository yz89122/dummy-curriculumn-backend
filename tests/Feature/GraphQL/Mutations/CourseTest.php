<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CourseTemplate;
use App\Models\Course;
use App\Models\User;

class CourseTest extends TestCase
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

    public function testCreateCourse()
    {
        $course_template = factory(CourseTemplate::class)->create();

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($course: CourseInput!) {
                    course {
                        create(course: $course) {
                            uuid
                            code
                            academic_year
                            academic_term
                            course_template {
                                uuid
                            }
                            course_times {
                                day_of_week
                                period
                            }
                        }
                    }
                }
            ', [
                'course' => [
                    'code' => $code = 'course',
                    'course_template_uuid' => $course_template->uuid,
                    'academic_year' => $year = now()->year,
                    'academic_term' => $term = 'Spring',
                    'course_times' => $course_times = [
                        [
                            'day_of_week' => 'Monday',
                            'period' => '_1',
                        ],
                        [
                            'day_of_week' => 'Monday',
                            'period' => '_2',
                        ],
                    ],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'course' => [
                        'create' => [
                            'code' => $code,
                            'course_template' => [
                                'uuid' => $course_template->uuid,
                            ],
                            'academic_year' => $year,
                            'academic_term' => $term,
                            'course_times' => $course_times,
                        ],
                    ],
                ],
            ]);

        $this->assertTrue(Course::where('code', $code)->exists());
    }

    public function testUpdateCourse()
    {
        $course_template = factory(CourseTemplate::class)->create();
        $new_course_template = factory(CourseTemplate::class)->create();
        $course = factory(Course::class)->create(['course_template_id' => $course_template->id]);



        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!, $course: CourseInput!) {
                    course {
                        update(uuid: $uuid, course: $course) {
                            uuid
                            code
                            academic_year
                            academic_term
                            course_template {
                                uuid
                            }
                            course_times {
                                day_of_week
                                period
                            }
                        }
                    }
                }
            ', [
                'uuid' => $course->uuid,
                'course' => [
                    'code' => $code = 'updated_course',
                    'course_template_uuid' => $new_course_template->uuid,
                    'academic_year' => $year = now()->year - 1,
                    'academic_term' => $term = 'Spring',
                    'course_times' => $course_times = [
                        [
                            'day_of_week' => 'Monday',
                            'period' => '_1',
                        ],
                        [
                            'day_of_week' => 'Monday',
                            'period' => '_2',
                        ],
                    ],
                ],
            ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    'course' => [
                        'update' => [
                            'code' => $code,
                            'course_template' => [
                                'uuid' => $new_course_template->uuid,
                            ],
                            'academic_year' => $year,
                            'academic_term' => $term,
                            'course_times' => $course_times,
                        ],
                    ],
                ],
            ]);

        $course->refresh();
        $this->assertEquals($code, $course->code);
    }

    public function testDeleteCourse()
    {
        $course_template = factory(CourseTemplate::class)->create();
        $course = factory(Course::class)->create(['course_template_id' => $course_template->id]);

        $this
            ->actingAs($this->administrator)
            ->graphQL('
                mutation ($uuid: String!) {
                    course {
                        delete(uuid: $uuid)
                    }
                }
            ', ['uuid' => $course->uuid]);

        $this->assertTrue($course->refresh()->trashed());
    }
}
