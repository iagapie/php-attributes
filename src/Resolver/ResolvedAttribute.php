<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

final class ResolvedAttribute
{
    /**
     * ResolvedAttribute constructor.
     * @param object $attribute
     * @param ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $target
     */
    public function __construct(
        private object $attribute,
        private ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $target
    ) {
    }

    /**
     * @return object
     */
    public function attribute(): object
    {
        return $this->attribute;
    }

    /**
     * @return ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter
     */
    public function target(): ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter
    {
        return $this->target;
    }
}