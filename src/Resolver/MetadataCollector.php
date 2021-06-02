<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

use function array_map;
use function array_merge;

class MetadataCollector
{
    /**
     * @param ReflectionClass $ref
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    public function attributes(ReflectionClass $ref, ?string $name = null, int $flags = 0): array
    {
        return array_merge(
            $this->classAttributes($ref, $name, $flags),
            $this->constantAttributes($ref, $name, $flags),
            $this->propertyAttributes($ref, $name, $flags),
            $this->methodAttributes($ref, $name, $flags),
            $this->parameterAttributes($ref, $name, $flags),
        );
    }

    /**
     * @param ReflectionClass $ref
     * @param string|null $name
     * @param int $flags
     * @return Metadata[]
     */
    public function classAttributes(ReflectionClass $ref, ?string $name = null, int $flags = 0): array
    {
        return $this->collect($ref, $name, $flags);
    }

    /**
     * @param ReflectionClass $ref
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    public function constantAttributes(ReflectionClass $ref, ?string $name = null, int $flags = 0): array
    {
        return $this->flatCollect($ref->getReflectionConstants(), $name, $flags);
    }

    /**
     * @param ReflectionClass $ref
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    public function propertyAttributes(ReflectionClass $ref, ?string $name = null, int $flags = 0): array
    {
        return $this->flatCollect($ref->getProperties(), $name, $flags);
    }

    /**
     * @param ReflectionClass $ref
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    public function methodAttributes(ReflectionClass $ref, ?string $name = null, int $flags = 0): array
    {
        return $this->flatCollect($ref->getMethods(), $name, $flags);
    }

    /**
     * @param ReflectionClass|ReflectionMethod $ref
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    public function parameterAttributes(
        ReflectionClass|ReflectionMethod $ref,
        ?string $name = null,
        int $flags = 0
    ): array {
        if ($ref instanceof ReflectionClass) {
            $target = $ref->getMethods();
            $parameters = array_map(fn($method) => $this->parameterAttributes($method, $name, $flags), $target);
            return array_merge(...$parameters);
        }

        return $this->flatCollect($ref->getParameters(), $name, $flags);
    }

    /**
     * @param ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $ref
     * @param string|null $name
     * @param int $flags
     * @return Metadata[]
     */
    public function collect(
        ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $ref,
        ?string $name = null,
        int $flags = 0
    ): array {
        return array_map(
            fn(ReflectionAttribute $attribute) => new Metadata($attribute, $ref),
            $ref->getAttributes($name, $flags)
        );
    }

    /**
     * @param array $target
     * @param string|null $name
     * @param int $flags
     * @return array
     */
    private function flatCollect(array $target, ?string $name = null, int $flags = 0): array
    {
        $mappedRef = array_map(
            fn($specificRef) => $this->collect($specificRef, $name, $flags),
            $target
        );

        return array_merge(...$mappedRef);
    }
}