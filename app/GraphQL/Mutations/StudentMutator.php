<?php

namespace App\GraphQL\Mutations;

use \Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Department;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicatedException;

class StudentMutator
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
        return DB::transaction(function () use ($args) {
            $student_attrs = array_merge($args['student'], ['user_id' => null]);
            throw_if(
                Student::where('code', $student_attrs['code'])->lockForUpdate()->exists(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            throw_unless(
                $department = Department::where('uuid', $student_attrs['department_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The department was not found'
            );
            $student_attrs['department_id'] = $department->id;
            if (array_key_exists('user_uuid', $student_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $student_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'User not found'
                );
                $student_attrs['user_id'] = $user->id;
            }
            return Student::create($student_attrs);
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
            throw_unless(
                $student = Student::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The student was not found'
            );
            $student_attrs = array_merge($args['student'], ['user_id' => null]);
            throw_unless(
                $department = Department::where('uuid', $student_attrs['department_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The department was not found'
            );
            $student_attrs['department_id'] = $department->id;
            if (array_key_exists('user_uuid', $student_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $student_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'User not found'
                );
                $student_attrs['user_id'] = $user->id;
            }
            $student->fill($student_attrs)->save();
            return $student;
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
                $student = Student::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The student was not found'
            );
            $student->delete();
        });
    }
}
