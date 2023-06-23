<?php

namespace Dustin\ImpEx\Serializer\Exception;

class DateConversionException extends AttributeConversionException
{
    public const ERROR_CODE = 'IMPEX_DATE_CONVERSION_ERROR';

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
