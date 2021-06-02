<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use ReflectionException;

interface Look
{
    public const ALL = 61;

    /**
     * @param int $flags Ex.: \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY
     * @return ResolvedAttribute[]
     * @throws ReflectionException
     */
    public function __invoke(int $flags = self::ALL): array;
}