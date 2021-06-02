<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionException;

interface AttributeResolver
{
    public const ALL = 61;

    /**
     * @param string|ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $ref
     * @param string|null $name
     * @param int $flags Ex.: \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY
     * @return ResolvedAttribute[]
     * @throws ReflectionException
     */
    public function resolve(
        string|ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $ref,
        ?string $name = null,
        int $flags = self::ALL
    ): array;
}