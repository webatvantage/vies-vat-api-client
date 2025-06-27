<?php

namespace DragonBe\Vies\Exceptions;

class InvalidVatNumberFormatException extends ViesException
{

    /**
     * @param string $countryCode
     * @param string $vatNumber
     */
    public function __construct(string $countryCode, string $vatNumber)
    {
        parent::__construct(sprintf(
            'Invalid VAT number format "%s" for country "%s"',
            $vatNumber,
            $countryCode,
        ));
    }
}