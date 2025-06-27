<?php

namespace DragonBe\Vies\Exceptions;

class CountryNoLongerSupportedException extends ViesServiceException
{
    public function __construct(string $name, string $excluded, string $reason)
    {
        parent::__construct(sprintf(
            'Country %s is no longer supported by VIES services provided by EC since %s because of %s',
            $name,
            $excluded,
            $reason
        ));
    }
}