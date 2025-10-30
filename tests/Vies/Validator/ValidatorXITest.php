<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorXITest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorXI
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('XI', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['925901618', true],
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
