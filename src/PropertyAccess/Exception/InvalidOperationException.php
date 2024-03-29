<?php

namespace Dustin\ImpEx\PropertyAccess\Exception;

use Dustin\Exception\ErrorCodeException;

class InvalidOperationException extends ErrorCodeException
{
    public const ERROR_CODE = 'IMPEX_PROPERTY_ACCESS__INVALID_OPERATION_ERROR';

    public function __construct(string $operation)
    {
        parent::__construct(
            '{{ operation }} is not a valid operation.',
            ['operation' => $operation]
        );
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
