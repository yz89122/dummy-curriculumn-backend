<?php

namespace Tests\Feature\GraphQL\Mutations;

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Models\User;
use App\Models\College;
use App\Models\Department;
use App\Models\Student;
use App\Models\CourseTemplate;
use App\Models\Course;
use App\Models\Selection;

class StudentOperationsTest extends TestCase
{
    use RefreshDatabase;
    use MakesGraphQLRequests;

    public function testSelect()
    {
        Config::set('app.academic_year', $year = now()->year);
        COnfig::set('app.academic_term', $term = 'Fall');
        $user = factory(User::class)->create();
        $college = factory(College::class)->create();
        $department = factory(Department::class)->create(['college_id' => $college->id]);
        $student = factory(Student::class)->create(['user_id' => $user->id, 'department_id' => $department->id]);
        $course_template = factory(CourseTemplate::class)->create();
        $course = factory(Course::class)->create([
            'course_template_id' => $course_template->id,
            'academic_year' => $year,
            'academic_term' => $term,
        ]);

        $this
            ->actingAs($user)
            ->graphQL('
                mutation ($course_code: String!) {
                    student_operations {
                        select(course_code: $course_code)
                    }
                }
            ', ['course_code' => $course->code])
            ->assertOk();

        $this->assertTrue(
            Selection::query()
                ->where('course_id', $course->id)
                ->where('student_id', $student->id)
                ->exists()
        );
    }
}
