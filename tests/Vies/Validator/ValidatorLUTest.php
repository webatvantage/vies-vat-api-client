<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorLUTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorLU
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('LU', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['10000356', true],
			['10000355', false],
			['1234567', false],
		];
	}
}
