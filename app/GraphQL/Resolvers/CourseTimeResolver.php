<?php

namespace App\GraphQL\Resolvers;

use App\Models\CourseTime;
use App\GraphQL\SimpleBatchLoader;
use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CourseTimeResolver
{
    public function period(CourseTime $course_time, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        return '_'.(string)$course_time->period;
    }
}
