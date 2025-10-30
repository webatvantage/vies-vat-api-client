<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorEE
 *
 */
class ValidatorEE extends VatValidator
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

		$checkVal = $this->sumWeights([3, 7, 1, 3, 7, 1, 3, 7], $vatNumber);
		$checkVal = (ceil($checkVal / 10) * 10) - $checkVal;

		return $checkVal == (int)$vatNumber[8];
	}
}
