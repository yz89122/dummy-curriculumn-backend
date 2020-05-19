<?php

namespace App\Listeners;

use Symfony\Component\Finder\Finder;
use Nuwave\Lighthouse\Events\BuildSchemaString;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AppendGraphQLEnums
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BuildSchemaString  $event
     * @return string
     */
    public function handle(BuildSchemaString $event)
    {
        return collect((new Finder)->files()->in(config('lighthouse.schema.enums')))
            ->map(function ($file) {
                return $file->getContents();
            })
            ->reduce(function ($carry, $item) {
                return $carry.$item;
            }, '');
    }
}
