<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\College;
use App\Models\Department;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\CourseTemplate;
use App\Models\Course;
use App\Models\Selection;
use App\Models\Administrator;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            factory(User::class, 100)->create();

            $colleges = factory(College::class, 3)->create();

            $departments = $colleges
                ->map(function (College $college) {
                    return factory(Department::class, 5)->create([
                        'college_id' => $college->id,
                    ]);
                })
                ->flatten();

            $students = $departments
                ->map(function (Department $department) {
                    return factory(Student::class, 50)
                        ->create([
                            'department_id' => $department->id,
                        ]);
                })
                ->flatten();

            $teachers = factory(Teacher::class, 100)->create();

            $templates = factory(CourseTemplate::class, 500)->create();

            $courses = $templates
                ->map(function (CourseTemplate $template) {
                    return factory(Course::class, 2)
                        ->create([
                            'course_template_id' => $template->id,
                        ]);
                })
                ->flatten();
            $courses->each(function (Course $course) use ($teachers) {
                $course->teachers()->saveMany($teachers->random(rand(1, 2)));
            });
            $students->each(function (Student $student) use ($courses) {
                $courses->random(rand(6, 10))->each(function (Course $course) use ($student) {
                    Selection::create([
                        'course_id' => $course->id,
                        'student_id' => $student->id,
                    ]);
                });
            });

            $admin = factory(User::class)->create(['username' => 'admin', 'display_name' => 'Administrator']);
            $admin->administrator()->create(['code' => '0000']);

            $student = factory(User::class)->create(['username' => 'student', 'display_name' => 'Student']);
            $student->student()->save(factory(Student::class)->make(['code' => '000000000', 'department_id' => $departments->random()->id]));

            $teacher = factory(User::class)->create(['username' => 'teacher', 'display_name' => 'Teacher']);
            $teacher->teacher()->create(['code' => '000000']);
        });
    }
}
