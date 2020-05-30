<?php

namespace App\GraphQL\Resolvers;

use App\Models\User;
use App\Models\Student;
use App\GraphQL\SimpleBatchLoader;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class StudentResolver
{
    public function display_name(Student $student, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        SimpleBatchLoader::instance(User::class)->add($student->user_id);
        return new Deferred(function () use ($student) {
            $user = SimpleBatchLoader::instance(User::class)->get($student->user_id);
            return $user ? $user->display_name : null;
        });
    }
}
