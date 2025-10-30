<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorATTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorAT
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function test_validator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('AT', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['U10223006', true],
			['U1022300', false],
			['A10223006', false],
			['U10223005', false],
		];
	}
}
