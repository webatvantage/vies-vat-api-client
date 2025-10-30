<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorSITest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorSI
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('SI', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['15012557', true],
			['15012556', false],
			['12345670', false],
			['01234567', false],
			['1234567', false],
			['95796550', true],
			['95736220', false],
		];
	}
}
