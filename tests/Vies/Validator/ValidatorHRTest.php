<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorHRTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorHR
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('HR', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['38192148118', true],
			['3819214811', false],
			['1234567890A', false],
			['AA123456789', false],
		];
	}
}
