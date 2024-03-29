<?php

namespace Dustin\ImpEx\Serializer\Converter\ArrayList;

use Dustin\ImpEx\Serializer\Converter\BidirectionalConverter;
use Dustin\ImpEx\Serializer\Converter\ConversionContext;
use Dustin\ImpEx\Util\Type;

class ArrayKeyConverter extends BidirectionalConverter
{
    public function __construct(private ArrayKeyConversionStrategy $strategy, string ...$flags)
    {
        parent::__construct(...$flags);
    }

    public function normalize(mixed $value, ConversionContext $context): mixed
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        $value = $this->ensureType($value, Type::ARRAY, $context);

        $keys = $this->strategy->normalizeKeys(array_keys($value), $context);

        return array_combine($keys, $value);
    }

    public function denormalize(mixed $value, ConversionContext $context): mixed
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        $value = $this->ensureType($value, Type::ARRAY, $context);

        $keys = $this->strategy->denormalizeKeys(array_keys($value), $context);

        return array_combine($keys, $value);
    }
}
