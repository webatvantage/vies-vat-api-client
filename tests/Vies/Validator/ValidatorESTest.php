<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorESTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorES
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('ES', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['A0011012B', true],
			['A78304516', true],
			['X5910266W', true],
			['K0011012B', false],
			['12345678', false],
			['K001A012B', false],
			['A0011012C', false],
			['Z5910266W', false],
			['J5910266W', false],
		];
	}
}
