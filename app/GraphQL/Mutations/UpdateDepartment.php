<?php

namespace App\GraphQL\Mutations;

use App\Models\College;
use App\Models\Department;
use App\Models\DepartmentI18n;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateDepartment
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
        return DB::transaction(function () use ($args) {
            throw_unless(
                $department = Department::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Department not found'
            );
            throw_unless(
                $college = College::where('uuid', $args['department']['college_uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'College Not Found'
            );
            $department->code = $args['department']['code'];
            $department->college_id = $college->id;
            $department->save();
            $department->i18n()->delete();
            $department->i18n()->saveMany(collect($args['department']['i18n'])->push([
                'locale' => 'default',
                'text' => $args['department']['default_text'],
            ])->mapInto(DepartmentI18n::class));
            return $department;
        });
    }
}
