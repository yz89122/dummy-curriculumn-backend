<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Illuminate\Support\Facades\DB;
use App\Models\Administrator;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicatedException;

class AdministratorMutator
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
            $admin_attrs = array_merge($args['administrator'], ['user_id' => null]);
            throw_if(
                Administrator::where('code', $admin_attrs['code'])->lockForUpdate()->exists(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            if (array_key_exists('user_uuid', $admin_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $admin_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'User not found'
                );
                $admin_attrs['user_id'] = $user->id;
            }
            return Administrator::create($admin_attrs);
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
            $admin_attrs = array_merge($args['administrator'], ['user_id' => null]);
            throw_unless(
                $administrator = Administrator::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The administrator was not found'
            );
            if (array_key_exists('user_uuid', $admin_attrs)) {
                throw_unless(
                    $user = User::where('uuid', $admin_attrs['user_uuid'])->lockForUpdate()->first(),
                    NotFoundException::class,
                    'The user was not found'
                );
                $admin_attrs['user_id'] = $user->id;
            }
            $administrator->fill($admin_attrs)->save();
            return $administrator;
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
                $administrator = Administrator::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'The administrator was not found'
            );
            $administrator->delete();
        });
    }
}
