<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make('log')->debug('');
        $this->app->make('log')->debug('Current Test: '.static::class.'::'.$this->getName());
    }
}
