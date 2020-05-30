<?php

namespace App\GraphQL\Mutations;

use \Closure;
use App\Models\College;
use App\Models\Department;
use App\Models\I18n;
use App\Exceptions\DuplicatedException;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class DepartmentMutator
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
            $args_department = $args['department'];
            throw_if(
                Department::where('code', $args_department['code'])->lockForUpdate()->exists(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            throw_unless(
                $college = College::where('uuid', $args_department['college_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The college was not found'
            );
            $department = Department::create(array_merge($args_department, ['college_id' => $college->id]));
            $department->i18n()->saveMany(collect($args_department['i18n'])->push([
                'locale' => 'default',
                'text' => $args['department']['default_text'],
            ])->mapInto(I18n::class));
            return $department;
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
                $department = Department::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Department not found'
            );

            $args_department = $args['department'];
            throw_unless(
                $college = College::where('uuid', $args_department['college_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'College Not Found'
            );
            $department->fill(array_merge($args_department, ['college_id' => $college->id]));
            $department->save();
            $department->i18n()->delete();
            $department->i18n()->saveMany(collect($args_department['i18n'])->push([
                'locale' => 'default',
                'text' => $args['department']['default_text'],
            ])->mapInto(I18n::class));
            return $department;
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
                $department = Department::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );

            $department->delete();
        });
    }
}
