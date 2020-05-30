<?php

namespace App\GraphQL\Mutations;

use Closure;
use App\Models\CourseTemplate;
use App\Models\I18n;
use App\Exceptions\DuplicatedException;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Facades\DB;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CourseTemplateMutator
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
            $template_attrs = array_merge($args['course_template'], ['i18n' => []]);
            throw_if(
                CourseTemplate::where('code', $template_attrs['code'])->lockForUpdate()->count(),
                DuplicatedException::class,
                'The code provided is already in use'
            );
            $template = CourseTemplate::create($template_attrs);
            $template->i18n()->saveMany(collect($template_attrs['i18n'])->push([
                'locale' => 'default',
                'text' => $template_attrs['default_text'],
            ])->mapInto(I18n::class));
            return $template;
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
            $template_attrs = array_merge($args['course_template'], ['i18n' => []]);
            throw_unless(
                $template = CourseTemplate::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $template->fill($template_attrs);
            $template->save();
            $template->i18n()->delete();
            $template->i18n()->saveMany(collect($template_attrs['i18n'])->push([
                'locale' => 'default',
                'text' => $template_attrs['default_text'],
            ])->mapInto(I18n::class));
            return $template;
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
                $template = CourseTemplate::where('uuid', $args['uuid'])->lockForUpdate()->first(),
                NotFoundException::class,
                'Not Found'
            );
            $template->delete();
        });
    }
}
