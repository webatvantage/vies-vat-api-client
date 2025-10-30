<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorPTTest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorPT
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('PT', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['502757191', true],
			['502757192', false],
			['12345678', false],
		];
	}
}
