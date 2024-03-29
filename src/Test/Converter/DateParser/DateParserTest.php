<?php

namespace Dustin\ImpEx\Test\Converter\DateParser;

use Dustin\ImpEx\Serializer\Converter\DateTime\DateParser;
use Dustin\ImpEx\Serializer\Converter\UnidirectionalConverter;
use Dustin\ImpEx\Test\Converter\UnidirectionalConverterTestCase;

class DateParserTest extends UnidirectionalConverterTestCase
{
    protected function instantiateConverter(array $params = []): UnidirectionalConverter
    {
        return new DateParser($params['format'], ...($params['flags'] ?? []));
    }
}
