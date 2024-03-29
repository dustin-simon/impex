<?php

namespace Dustin\ImpEx\Serializer\Converter\ArrayList;

use Dustin\ImpEx\PropertyAccess\Path;
use Dustin\ImpEx\Serializer\Converter\BidirectionalConverter;
use Dustin\ImpEx\Serializer\Converter\ConversionContext;
use Dustin\ImpEx\Serializer\Exception\AttributeConversionExceptionInterface;
use Dustin\ImpEx\Serializer\Exception\AttributeConversionExceptionStack;
use Dustin\ImpEx\Util\Type;

class ConcatConverter extends BidirectionalConverter
{
    public function __construct(private string $separator, string ...$flags)
    {
        parent::__construct(...$flags);
    }

    public function normalize(mixed $value, ConversionContext $context): array|string|null
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        if ($this->hasFlags(self::REVERSE)) {
            $value = $this->ensureType($value, Type::STRING, $context);

            return $this->explode($value);
        }

        $value = $this->ensureType($value, Type::ARRAY, $context);
        $this->validateStrings($value, $context);

        return $this->implode($value);
    }

    public function denormalize(mixed $value, ConversionContext $context): array|string|null
    {
        if ($this->hasFlags(self::SKIP_NULL) && $value === null) {
            return null;
        }

        if ($this->hasFlags(self::REVERSE)) {
            $value = $this->ensureType($value, Type::ARRAY, $context);
            $this->validateStrings($value, $context);

            return $this->implode($value);
        }

        $value = $this->ensureType($value, Type::STRING, $context);

        return $this->explode($value);
    }

    public function explode(string $value): array
    {
        return explode($this->separator, $value);
    }

    public function implode(array $value): string
    {
        return implode($this->separator, $value);
    }

    private function validateStrings(array $strings, ConversionContext $context): void
    {
        $exceptions = new AttributeConversionExceptionStack($context->getPath(), $context->getRootData());

        foreach ($strings as $key => $v) {
            try {
                $this->validateStringConvertable($v, $context->subContext(new Path([$key])));
            } catch (AttributeConversionExceptionInterface $e) {
                $exceptions->add($e);
            }
        }

        $exceptions->throw();
    }
}
