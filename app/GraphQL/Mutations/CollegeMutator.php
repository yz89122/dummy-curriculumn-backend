<?php

namespace App\GraphQL\Mutations;

use Closure;
use App\Models\College;
use App\Models\I18n;
use App\Exceptions\DuplicatedException;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CollegeMutator
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
            $college_attrs = array_merge($args['college'], ['i18n' => []]);
            throw_if(
                College::where('code', $college_attrs['code'])->lockForUpdate()->count(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            $college = College::create($college_attrs);
            $college->i18n()->saveMany(collect($college_attrs['i18n'])->push([
                'locale' => 'default',
                'text' => $college_attrs['default_text'],
            ])->mapInto(I18n::class));
            return $college;
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
            $college_attrs = array_merge($args['college'], ['i18n' => []]);
            throw_unless(
                $college = College::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $college->fill($college_attrs);
            $college->save();
            $college->i18n()->delete();
            $college->i18n()->saveMany(collect($college_attrs['i18n'])->push([
                'locale' => 'default',
                'text' => $college_attrs['default_text'],
            ])->mapInto(I18n::class));
            return $college;
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
                $college = College::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $college->delete();
        });
    }
}
