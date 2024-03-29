<?php

declare(strict_types=1);

namespace Dustin\ImpEx\PropertyAccess\Operation;

use Dustin\ImpEx\PropertyAccess\Exception\InvalidOperationException;
use Dustin\ImpEx\PropertyAccess\Path;
use Dustin\ImpEx\PropertyAccess\PropertyAccessor;

class AccessOperation
{
    public const GET = 'get';

    public const SET = 'set';

    public const PUSH = 'push';

    public const MERGE = 'merge';

    public const COLLECT = 'collect';

    public const HAS = 'has';

    public const WRITE_OPERATIONS = [
        AccessOperation::PUSH,
        AccessOperation::SET,
        AccessOperation::MERGE,
    ];

    public const READ_OPERATIONS = [
        AccessOperation::GET,
        AccessOperation::COLLECT,
        AccessOperation::HAS,
    ];

    /**
     * @var array
     */
    private $flags = [];

    public function __construct(
        private string|array|Path $path,
        private string $operation,
        string ...$flags
    ) {
        $this->flags = $flags;
    }

    public static function isWriteOperation(string|self $operation): bool
    {
        $operation = is_string($operation) ? $operation : $operation->getOperation();

        return \in_array($operation, self::WRITE_OPERATIONS);
    }

    public static function isReadOperation(string|self $operation): bool
    {
        $operation = is_string($operation) ? $operation : $operation->getOperation();

        return \in_array($operation, self::READ_OPERATIONS);
    }

    public static function get(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::GET, ...$flags);
    }

    public static function set(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::SET, ...$flags);
    }

    public static function push(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::PUSH, ...$flags);
    }

    public static function merge(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::MERGE, ...$flags);
    }

    public static function collect(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::COLLECT, ...$flags);
    }

    public static function has(string|array|Path $path, string ...$flags): self
    {
        return new self($path, self::HAS, ...$flags);
    }

    public function execute(mixed &$data, mixed $value = null): mixed
    {
        switch ($this->operation) {
            case self::GET:
                return PropertyAccessor::get($this->getPath(), $data, ...$this->getFlags());
            case self::SET:
                return PropertyAccessor::set($this->getPath(), $data, $value, ...$this->getFlags());
            case self::PUSH:
                return PropertyAccessor::push($this->getPath(), $data, $value, ...$this->getFlags());
            case self::MERGE:
                return PropertyAccessor::merge($this->getPath(), $data, $value, ...$this->getFlags());
            case self::COLLECT:
                return PropertyAccessor::collect($this->getPath(), $data, ...$this->getFlags());
            case self::HAS:
                return PropertyAccessor::has($this->getPath(), $data, ...$this->getFlags());
        }

        throw new InvalidOperationException($this->operation);
    }

    public function getPath(): string|array|Path
    {
        return $this->path;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }
}
