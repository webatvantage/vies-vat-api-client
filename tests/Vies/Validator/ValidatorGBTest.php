<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

use Webatvantage\Vies\Exceptions\ViesException;

class ValidatorGBTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorGB
	 *
	 * @dataProvider vatNumberProvider
	 *
	 * @throws ViesException
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('GB', $vatNumber, $state, true);
	}

	public function vatNumberProvider(): array
	{
		return [
			['434031494', true],
			['GD001', true],
			['HA500', true],
			['434031493', false],
			['12345', false],
			['GD500', false],
			['HA100', false],
			['12345678', false],
		];
	}
}
