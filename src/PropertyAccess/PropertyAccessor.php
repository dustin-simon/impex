<?php

namespace Dustin\ImpEx\PropertyAccess;

use Dustin\ImpEx\PropertyAccess\Exception\NotAccessableException;
use Dustin\ImpEx\Util\Type;

final class PropertyAccessor extends Accessor
{
    public const NULL_ON_ERROR = 'null_on_error';

    private static $accessors = [];

    private static bool $initialized = false;

    /**
     * @var string[]
     */
    private $flags = [];

    public function __construct(
        private string $path,
        string ...$flags
    ) {
        $this->flags = $flags;
    }

    public static function registerAccessor(string $accessor): void
    {
        if (!static::$initialized) {
            static::initialize();
        }

        static::validateAccessor($accessor);

        foreach ($accessor::getSupportedTypes() as $type) {
            static::$accessors[$type] = $accessor;
        }
    }

    public static function getSupportedTypes(): array
    {
        return array_keys(static::$accessors);
    }

    public static function get(string $path, mixed $data, string ...$flags): mixed
    {
        if (!static::$initialized) {
            static::initialize();
        }

        $pointer = $data;

        if (empty($path) || $pointer === null) {
            return $pointer;
        }

        $currentPath = '';

        foreach (explode('.', $path) as $field) {
            $currentPath = trim($currentPath .= ".$field", '.');

            $pointer = static::getValueOf($field, $pointer, $currentPath, ...$flags);

            if ($pointer === null) {
                return null;
            }
        }

        return $pointer;
    }

    public static function set(string $path, mixed $value, mixed &$data, string ...$flags): void
    {
        if (!static::$initialized) {
            static::initialize();
        }

        $pointer = $data;

        if (empty($path) || $pointer === null) {
            return;
        }

        $chain = [];

        foreach (explode('.', $path) as $field) {
            $pointer = static::getValueOf($field, $pointer, $path, ...$flags);
            $chain[] = [
                'field' => $field,
                'value' => $pointer,
            ];
        }

        $element = array_pop($chain);
        $element['value'] = $value;

        foreach (array_reverse($chain) as $record) {
            $holder = $record['value'];
            static::setValueOf($element['field'], $element['value'], $holder, $path);
            $element = [
                'field' => $record['field'],
                'value' => $holder,
            ];
        }

        static::setValueOf($element['field'], $element['value'], $data, $path);
    }

    public static function getValueOf(string $field, mixed $value, ?string $path, string ...$flags): mixed
    {
        if (!static::$initialized) {
            static::initialize();
        }

        if ($path === null) {
            $path = $field;
        }

        $accessor = static::getAccessor($value);

        if ($accessor === null) {
            if (!static::hasFlag(self::NULL_ON_ERROR, $flags)) {
                throw new NotAccessableException($path, Type::getType($value));
            }

            return null;
        }

        return $accessor::getValueOf($field, $value, $path, ...$flags);
    }

    public static function setValueOf(string $field, mixed $value, mixed &$data, ?string $path, string ...$flags): void
    {
        if (!static::$initialized) {
            static::initialize();
        }

        $accessor = static::getAccessor($data);

        if ($accessor === null) {
            if (!static::hasFlag(self::NULL_ON_ERROR, $flags)) {
                throw new NotAccessableException($path, Type::getType($data));
            }

            return;
        }

        $accessor::setValueOf($field, $value, $data, $path, ...$flags);
    }

    public static function getAccessor(mixed $value): ?string
    {
        foreach (array_reverse(static::$accessors) as $type => $accessor) {
            if (Type::is($value, $type)) {
                return $accessor;
            }
        }

        return null;
    }

    private static function initialize(): void
    {
        static::$initialized = true;

        static::registerAccessor(ObjectAccessor::class);
        static::registerAccessor(ArrayAccessor::class);
        static::registerAccessor(ContainerAccessor::class);
        static::registerAccessor(EncapsulationAccessor::class);
    }

    private static function validateAccessor(string $accessor): void
    {
        if (
            !class_exists($accessor) ||
            !is_subclass_of($accessor, Accessor::class) ||
            (new \ReflectionClass($accessor))->isAbstract()
        ) {
            throw new \InvalidArgumentException(sprintf('Accessor must be class inheriting from %s. Got %s.', Accessor::class, Type::getDebugType($accessor)));
        }
    }

    public function getValue(mixed $data): mixed
    {
        return static::get($this->path, $data, ...$this->flags);
    }
}
