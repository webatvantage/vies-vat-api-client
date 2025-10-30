<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorDKTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorDK
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('DK', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['88146328', true],
			['88146327', false],
			['1234567', false],
		];
	}
}
