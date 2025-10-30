<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorLVTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorLV
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('LV', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['40003009497', true],
			['40013009497', false],
			['40003009496', false],
			['1234567890', false],
			['00212345678', false],
		];
	}
}
