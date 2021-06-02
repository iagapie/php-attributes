<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

final class Metadata
{
    /**
     * Metadata constructor.
     * @param ReflectionAttribute $attribute
     * @param ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $target
     */
    public function __construct(
        private ReflectionAttribute $attribute,
        private ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $target
    ) {
    }

    /**
     * @return ReflectionAttribute
     */
    public function attribute(): ReflectionAttribute
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