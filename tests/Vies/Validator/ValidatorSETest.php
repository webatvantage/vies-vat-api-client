<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorSETest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorSE
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('SE', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['556188840401', true],
			['556188840400', false],
			['1234567890', false],
			['556181140401', false],
		];
	}
}
