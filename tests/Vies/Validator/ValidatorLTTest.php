<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorLTTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorLT
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state): void
	{
		$this->validateVatNumber('LT', $vatNumber, $state);
	}

	/**
	 * @return array<int, array{0: string, 1: bool}>
	 */
	public function vatNumberProvider(): array
	{
		return [
			['210061371310', true],
			['213179412', true],
			['290061371314', true],
			['208640716', true],
			['213179422', false],
			['21317941', false],
			['1234567890', false],
			['1234567890AB', false],
		];
	}
}
