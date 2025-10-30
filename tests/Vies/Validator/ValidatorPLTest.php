<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorPLTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorPL
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('PL', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['5260001246', true],
			['12342678090', false],
			['1212121212', false],
		];
	}
}
