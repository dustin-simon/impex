<?php

namespace Dustin\ImpEx\Serializer\Converter\String;

use Dustin\ImpEx\Serializer\Converter\ConversionContext;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;
use Dustin\ImpEx\Util\Type;

class StringPosition extends UnidirectionalConverter
{
    public function __construct(
        private string $needle,
        private int $offset = 0,
        private int $add = 0,
        string ...$flags
    ) {
        parent::__construct(...$flags);
    }

    public function convert(mixed $value, ConversionContext $context): mixed
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        $value = $this->ensureType($value, Type::STRING, $context);

        $result = mb_strpos($value, $this->needle, $this->offset);

        return $result !== false ? $result + $this->add : null;
    }
}
