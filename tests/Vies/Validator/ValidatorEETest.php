<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorEETest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorEE
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('EE', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['100207415', true],
			['1002074', false],
			['A12345678', false],
		];
	}
}
