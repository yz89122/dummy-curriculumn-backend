<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Course;
use App\Models\CourseTime;
use Faker\Generator as Faker;

$factory->define(CourseTime::class, function (Faker $faker) {
    return [
        'day_of_week' => collect(CourseTime::DAY_OF_WEEK)->random(),
        'period' => collect(CourseTime::PERIOD)->random(),
    ];
});

$factory->afterCreating(Course::class, function (Course $course, Faker $faker) {
    $c = collect(CourseTime::DAY_OF_WEEK)->count() * ($b = collect(CourseTime::PERIOD)->count());
    $table = collect();
    for ($i = 0; $i < $c; $i++) {
        $table->push($i);
    }
    $table->random(rand(1, 4))
        ->map(function ($cell) use ($b, $course) {
            return new CourseTime([
                'course_id' => $course->id,
                'day_of_week' => CourseTime::DAY_OF_WEEK[intdiv($cell, $b)],
                'period' => CourseTime::PERIOD[$cell % $b],
            ]);
        })
        ->each(function ($time) {
            $time->save();
        });
});
