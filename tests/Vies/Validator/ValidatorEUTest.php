<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorEUTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorEU
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('EU', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['372009975', false],
			['826409867', false],
			['528003555', false],
		];
	}
}
