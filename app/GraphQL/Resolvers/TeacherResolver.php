<?php

namespace App\GraphQL\Resolvers;

use App\Models\User;
use App\Models\Teacher;
use App\GraphQL\SimpleBatchLoader;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class TeacherResolver
{
    public function display_name(Teacher $teacher, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        SimpleBatchLoader::instance(User::class)->add($teacher->user_id);
        return new Deferred(function () use ($teacher) {
            return SimpleBatchLoader::instance(User::class)->get($teacher->user_id)->display_name;
        });
    }
}
