<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Webatvantage\Vies\Api\VatEuropeApi;
use Webatvantage\Vies\Exceptions\ViesException;
use Webatvantage\Vies\Vies;

abstract class AbstractValidatorTest extends TestCase
{
	/**
	 * @throws ViesException
	 */
	protected function validateVatNumber(string $country, string $vatNumber, bool $state, bool $useExcludedCountries = false)
	{
		$vies = new Vies(new VatEuropeApi());

		$this->assertSame($state, $vies->validateVatSum($country, $vatNumber, $useExcludedCountries));
	}
}
