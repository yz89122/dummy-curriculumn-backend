<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\Auth;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\GraphQL\Exceptions\AuthenticationException;

class Authenticate
{
    public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        throw_unless(
            $token = Auth::attempt(['username' => $args['username'], 'password' => $args['password']]),
            AuthenticationException::class,
            'Incorrect username or password'
        );

        return [
            'access_token' => $token,
            'expires_in' => Auth::factory()->getTTL() * 60,
        ];
    }
}
