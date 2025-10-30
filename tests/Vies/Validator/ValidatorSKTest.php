<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorSKTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorSK
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('SK', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['4030000007', true],
			['4030000006', false],
			['123456789', false],
			['0123456789', false],
			['4060000007', false],
		];
	}
}
