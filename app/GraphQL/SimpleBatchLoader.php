<?php

namespace App\GraphQL;

class SimpleBatchLoader
{
    public static function instance(string $class, string $column = 'id')
    {
        // ex: \App\GraphQL\SimpleBatchLoader:\App\Models\User,id
        $abstract = static::class.':'.$class.','.$column;

        // uses services container as context instead of global
        if (!app()->bound($abstract)) {
            app()->singleton($abstract, function () use ($class, $column) {
                return new static($class, $column);
            });
        }

        return app($abstract);
    }

    protected $class;

    protected $column;

    protected $pending;

    protected $loaded;

    public function __construct($class, $column = 'id')
    {
        $this->class = $class;
        $this->column = $column;
        $this->pending = collect();
        $this->loaded = collect();
    }

    public function add($id)
    {
        if (is_null($this->loaded->get($id))) {
            $this->pending->put($id, true);
        }
    }

    public function load()
    {
        if ($this->pending->count() <= 0) {
            return;
        }

        $models = $this->class::whereIn($this->column, $this->pending->keys())->get();
        $models->each(function ($model) {
            $this->loaded->put($model->{$this->column}, $model);
        });

        $this->pending = collect();
    }

    public function get($id)
    {
        $this->load();
        return $this->loaded->get($id);
    }
}
