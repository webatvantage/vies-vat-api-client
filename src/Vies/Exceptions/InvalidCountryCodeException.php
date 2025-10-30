<?php

namespace Webatvantage\Vies\Exceptions;

class InvalidCountryCodeException extends ViesException
{
	public function __construct(string $countryCode)
	{
		parent::__construct(sprintf('Invalid country code "%s" provided', $countryCode));
	}
}
