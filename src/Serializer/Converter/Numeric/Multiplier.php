<?php

namespace Dustin\ImpEx\Serializer\Converter\Numeric;

use Dustin\ImpEx\Serializer\Converter\BidirectionalConverter;
use Dustin\ImpEx\Serializer\Converter\ConversionContext;
use Dustin\ImpEx\Serializer\Exception\AttributeConversionException;
use Dustin\ImpEx\Util\Type;

class Multiplier extends BidirectionalConverter
{
    use NumberConversionTrait;

    public const DIVISION_BY_ZERO_ERROR = 'IMPEX_CONVERSION__DIVISION_BY_ZERO_ERROR';

    public function __construct(private int|float $factor, string ...$flags)
    {
        parent::__construct(...$flags);
    }

    public function normalize(mixed $value, ConversionContext $context): int|float|null
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        if (!$this->hasFlags(self::STRICT) && !Type::is($value, Type::NUMERIC)) {
            $this->validateNumericConvertable($value, $context);

            $value = $this->convertToNumeric($value);
        }

        $this->validateType($value, Type::NUMERIC, $context);

        return $value * $this->factor;
    }

    public function denormalize(mixed $value, ConversionContext $context): int|float|null
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        if (!$this->hasFlags(self::STRICT) && !Type::is($value, Type::NUMERIC)) {
            $this->validateNumericConvertable($value, $context);

            $value = $this->convertToNumeric($value);
        }

        $this->validateType($value, Type::NUMERIC, $context);

        if (floatval($this->factor) === 0.0) {
            throw new AttributeConversionException($context->getPath(), $context->getRootData(), 'Division by zero was detected.', [], self::DIVISION_BY_ZERO_ERROR);
        }

        return $value / $this->factor;
    }
}
