<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorFI
 *
 */
class ValidatorFI extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 8)
		{
			return false;
		}

		$weights = [7, 9, 10, 5, 8, 4, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);

		return (0 === $checkVal % 11)
			? (int) $vatNumber[7] === 0
			: 11 - ($checkVal % 11) == (int) $vatNumber[7];
	}
}
