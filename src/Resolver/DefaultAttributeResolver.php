<?php

declare(strict_types=1);

namespace IA\PhpAttributes\Resolver;

use Attribute;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

use function array_filter;
use function array_map;
use function class_exists;

class DefaultAttributeResolver implements AttributeResolver
{
    protected MetadataCollector $collector;

    /**
     * DefaultAttributeResolver constructor.
     * @param MetadataCollector|null $collector
     */
    public function __construct(?MetadataCollector $collector = null)
    {
        $this->collector = $collector ?? new MetadataCollector();
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(
        string|ReflectionClass|ReflectionClassConstant|ReflectionProperty|ReflectionMethod|ReflectionParameter $ref,
        ?string $name = null,
        int $flags = self::ALL
    ): array {
        if (!($flags & self::ALL)) {
            return [];
        }

        if (is_string($ref)) {
            $ref = new ReflectionClass($ref);
        }

        $resolvers = [
            Attribute::TARGET_CLASS => fn() => $this->resolveClass($ref, $name),
            Attribute::TARGET_CLASS_CONSTANT => fn() => $this->resolveConstant($ref, $name),
            Attribute::TARGET_PROPERTY => fn() => $this->resolveProperty($ref, $name),
            Attribute::TARGET_METHOD => fn() => $this->resolveMethod($ref, $name),
            Attribute::TARGET_PARAMETER => fn() => $this->resolveParameter($ref, $name),
        ];

        $attributes = [];

        foreach ($resolvers as $flag => $resolver) {
            if ($flag & $flags && ($data = $resolver())) {
                $attributes = array_merge($attributes, $data);
            }
        }

        return $this->instantiate($attributes);
    }

    protected function instantiate(array $attributes): array
    {
        $validAttributes = array_filter(
            $attributes,
            fn(Metadata $metadata) => class_exists($metadata->attribute()->getName())
        );

        return array_map(
            fn(Metadata $metadata) => new ResolvedAttribute(
                $metadata->attribute()->newInstance(),
                $metadata->target()
            ),
            $validAttributes
        );
    }

    protected function resolveClass($ref, string $name): array
    {
        if ($ref instanceof ReflectionClass) {
            return $this->collector->classAttributes($ref, $name, 2);
        }

        return [];
    }

    protected function resolveConstant($ref, string $name): array
    {
        if ($ref instanceof ReflectionClass) {
            return $this->collector->constantAttributes($ref, $name, 2);
        }

        if ($ref instanceof ReflectionClassConstant) {
            return $this->collector->collect($ref, $name, 2);
        }

        return [];
    }

    protected function resolveProperty($ref, string $name): array
    {
        if ($ref instanceof ReflectionClass) {
            return $this->collector->propertyAttributes($ref, $name, 2);
        }

        if ($ref instanceof ReflectionProperty) {
            return $this->collector->collect($ref, $name, 2);
        }

        return [];
    }

    protected function resolveMethod($ref, string $name): array
    {
        if ($ref instanceof ReflectionClass) {
            return $this->collector->methodAttributes($ref, $name, 2);
        }

        if ($ref instanceof ReflectionMethod) {
            return $this->collector->collect($ref, $name, 2);
        }

        return [];
    }

    protected function resolveParameter($ref, string $name): array
    {
        if ($ref instanceof ReflectionClass || $ref instanceof ReflectionMethod) {
            return $this->collector->parameterAttributes($ref, $name, 2);
        }

        if ($ref instanceof ReflectionParameter) {
            return $this->collector->collect($ref, $name, 2);
        }

        return [];
    }
}