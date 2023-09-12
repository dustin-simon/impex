<?php

namespace Dustin\ImpEx\PropertyAccess;

use Dustin\Encapsulation\Container;
use Dustin\ImpEx\PropertyAccess\Exception\PropertyNotFoundException;

class ContainerAccessor extends Accessor
{
    public static function getSupportedTypes(): array
    {
        return [Container::class];
    }

    public static function getValueOf(string $field, mixed $value, ?string $path, string ...$flags): mixed
    {
        if (!is_numeric($field)) {
            if (!static::hasFlag(self::NULL_ON_ERROR, $flags)) {
                throw new PropertyNotFoundException($path);
            }

            return null;
        }

        return static::fromContainer(intval($field), $value, $path, ...$flags);
    }

    public static function fromContainer(int $index, Container $container, ?string $path, string ...$flags): mixed
    {
        if ($path === null) {
            $path = (string) $index;
        }

        $elements = $container->toArray();

        if (!array_key_exists($index, $elements)) {
            if (!static::hasFlag(self::NULL_ON_ERROR, $flags)) {
                throw new PropertyNotFoundException($path);
            }

            return null;
        }

        return $elements[$index];
    }
}
