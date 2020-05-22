<?php

namespace App\GraphQL\Scalars;

use Carbon\Carbon;
use Exception;
use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class VoidScalar extends ScalarType
{
    /** @var string */
    public $name = 'Void';

    /** @var string */
    public $description = 'void';

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @throws Error
     */
    public function serialize($value)
    {
    }

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @throws Error
     */
    public function parseValue($value)
    {
    }

    /**
     * @param Node         $valueNode
     * @param mixed[]|null $variables
     *
     * @return void
     *
     * @throws Exception
     */
    public function parseLiteral($valueNode, ?array $variables = null)
    {
    }
}
