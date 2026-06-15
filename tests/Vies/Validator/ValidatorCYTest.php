<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorCYTest extends AbstractValidatorTest
{
	/**
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state): void
	{
		$this->validateVatNumber('CY', $vatNumber, $state);
	}

	/**
	 * @return array<int, array{0: string, 1: bool}>
	 */
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
