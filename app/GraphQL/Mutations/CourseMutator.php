<?php

namespace App\GraphQL\Mutations;

use Closure;
use App\Models\Course;
use App\Models\CourseTime;
use App\Models\CourseTemplate;
use App\Models\I18n;
use App\Exceptions\DuplicatedException;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CourseMutator
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return [
            'create' => Closure::fromCallable([$this, 'create']),
            'update' => Closure::fromCallable([$this, 'update']),
            'delete' => Closure::fromCallable([$this, 'delete']),
        ];
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return DB::transaction(function () use ($rootValue, $args) {
            $course_attrs = $args['course'];
            throw_if(
                Course::where('code', $course_attrs['code'])->lockForUpdate()->exists(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            throw_unless(
                $template = CourseTemplate::where('uuid', $course_attrs['course_template_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The course template was not found'
            );
            $course_attrs['course_template_id'] = $template->id;
            $course = Course::create($course_attrs);
            $course->course_times()->saveMany(
                collect($course_attrs['course_times'])
                    ->map(function ($item) {
                        return [
                            'day_of_week' => $item['day_of_week'],
                            'period' => substr($item['period'], 1),
                        ];
                    })
                    ->mapInto(CourseTime::class)
            );
            return $course;
        });
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function update($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return DB::transaction(function () use ($args) {
            $course_attrs = $args['course'];
            throw_unless(
                $course = Course::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The course was not found'
            );
            throw_unless(
                $template = CourseTemplate::where('uuid', $course_attrs['course_template_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The course template was not found'
            );
            $course_attrs['course_template_id'] = $template->id;
            $course->fill($course_attrs)->save();
            $course->course_times()->delete();
            $course->course_times()->saveMany(
                collect($course_attrs['course_times'])
                    ->map(function ($item) {
                        return [
                            'day_of_week' => $item['day_of_week'],
                            'period' => substr($item['period'], 1),
                        ];
                    })
                    ->mapInto(CourseTime::class)
            );
            return $course;
        });
    }

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function delete($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return DB::transaction(function () use ($args) {
            throw_unless(
                $course = Course::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $course->delete();
        });
    }
}
