<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorELTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorEL
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('EL', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['040127797', true],
			['040127796', false],
			['1234567', false],
		];
	}
}
