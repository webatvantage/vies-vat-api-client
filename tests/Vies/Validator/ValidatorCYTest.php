<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorCYTest extends AbstractValidatorTest
{
	/**
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('CY', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['00532445O', true],
			['005324451', false],
			['0053244511', false],
			['12000139V', false],
			['72000139V', false],
		];
	}
}
