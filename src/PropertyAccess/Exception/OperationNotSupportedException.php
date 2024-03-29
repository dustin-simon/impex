<?php

namespace Dustin\ImpEx\PropertyAccess\Exception;

use Dustin\Exception\ErrorCodeException;

class OperationNotSupportedException extends ErrorCodeException
{
    public const ERROR_CODE = 'IMPEX_PROPERTY_ACCESS__OPERATION_NOT_SUPPORTED_ERROR';

    public function __construct(string $operation)
    {
        parent::__construct(
            'Operation {{ operation }} is not supported.',
            ['operation' => $operation]
        );
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
