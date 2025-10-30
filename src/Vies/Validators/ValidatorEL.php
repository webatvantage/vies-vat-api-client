<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorEL
 *
 */
class ValidatorEL extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 9)
		{
			return false;
		}

		$weights = [256, 128, 64, 32, 16, 8, 4, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = ($checkVal % 11) > 9 ? 0 : ($checkVal % 11);

		return $checkVal === (int) $vatNumber[8];
	}
}
