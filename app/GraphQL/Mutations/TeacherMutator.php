<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicatedException;

class TeacherMutator
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
    public function create($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return DB::transaction(function () use ($args) {
            $teacher_attrs = array_merge($args['teacher'], ['user_id' => null]);
            throw_if(
                Teacher::where('code', $teacher_attrs['code'])->lockForUpdate()->exists(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            if (array_key_exists('user_uuid', $teacher_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $teacher_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'User not found'
                );
                $teacher_attrs['user_id'] = $user->id;
            }
            return Teacher::create($teacher_attrs);
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
            $teacher_attrs = array_merge($args['teacher'], ['user_id' => null]);
            throw_unless(
                $teacher = Teacher::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The teacher was not found'
            );
            if (array_key_exists('user_uuid', $teacher_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $teacher_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'The user was not found'
                );
                $teacher_attrs['user_id'] = $user->id;
            }
            $teacher->fill($teacher_attrs)->save();
            return $teacher;
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
                $teacher = Teacher::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The teacher was not found'
            );
            $teacher->delete();
        });
    }
}
