<?php

namespace App\GraphQL\Resolvers;

use App\Models\User;
use App\Models\Administrator;
use App\GraphQL\SimpleBatchLoader;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class AdministratorResolver
{
    public function display_name(Administrator $administrator, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        SimpleBatchLoader::instance(User::class)->add($administrator->user_id);
        return new Deferred(function () use ($administrator) {
            $user = SimpleBatchLoader::instance(User::class)->get($administrator->user_id);
            return $user ? $user->display_name : null;
        });
    }
}
