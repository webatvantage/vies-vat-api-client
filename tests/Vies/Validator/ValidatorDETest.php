<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Validator;

class ValidatorDETest extends AbstractValidatorTest
{
	/**
	 * @covers \Webatvantage\Vies\Validators\ValidatorDE
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testValidator(string $vatNumber, bool $state)
	{
		$this->validateVatNumber('DE', $vatNumber, $state);
	}

	public function vatNumberProvider(): array
	{
		return [
			['111111125', true],
			['111111124', false],
			['1234567', false],
		];
	}
}
