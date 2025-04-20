<?php

namespace Dklementjev\Phpstan\BadIdea\Helper;

use PHPStan\Reflection\AttributeReflection;

class ReflectionHelper
{
    /**
     * @param AttributeReflection[] $attributes
     * 
     * @return AttributeReflection[]
     */
    public static function findAttributesByClass(string $className, array $attributes): array
    {
        return array_values(
            array_filter(
                $attributes,
                static fn (AttributeReflection $item): bool => $item->getName() === $className
            )
        );
    }

    public static function getAttributeArgument(AttributeReflection $attr, string $name, $defaultValue = null): mixed
    {
        $argTypes = $attr->getArgumentTypes();
        $argValue = $argTypes[$name] ?? null;

        return $argValue ? $argValue->getValue() : $defaultValue;
    }
}