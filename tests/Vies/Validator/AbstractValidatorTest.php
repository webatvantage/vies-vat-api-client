<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

use PHPUnit\Framework\TestCase;
use Webatvantage\Vies\Api\VatEuropeApi;
use Webatvantage\Vies\Vies;

abstract class AbstractValidatorTest extends TestCase
{
	protected function validateVatNumber(string $country, string $vatNumber, bool $state)
	{
		$vies = new Vies(new VatEuropeApi());

		$this->assertSame($state, $vies->validateVatSum($country, $vatNumber));
	}
}
