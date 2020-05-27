<?php

namespace App\GraphQL\Mutations;

use App\Models\College;
use App\Models\I18n;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateCollege
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
                $college = College::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $college->code = $args['college']['code'];
            $college->save();
            $college->i18n()->delete();
            $college->i18n()->saveMany(collect($args['college']['i18n'])->push([
                'locale' => 'default',
                'text' => $args['college']['default_text'],
            ])->mapInto(I18n::class));
            return $college;
        });
    }
}
