<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorHUTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorHU
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state): void
	{
		$this->validateVatNumber('HU', $vatNumber, $state);
	}

	/**
	 * @return array<int, array{0: string, 1: bool}>
	 */
	public function vatNumberProvider(): array
	{
		return [
			['21376414', true],
			['10597190', true],
			['2137641', false],
			['1234567A', false],
		];
	}
}
