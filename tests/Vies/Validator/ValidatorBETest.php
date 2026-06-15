<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorBETest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorBE
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state): void
	{
		$this->validateVatNumber('BE', $vatNumber, $state);
	}

	/**
	 * @return array<int, array{0: string, 1: bool}>
	 */
	public function vatNumberProvider(): array
	{
		return [
			['776091951', false],
			['0776091952', false],
			['07760919', false],
			['0417710407', true],
			['0627515170', true],
		];
	}
}
