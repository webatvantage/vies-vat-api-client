<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorPT
 *
 */
class ValidatorPT extends VatValidator
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

		$checksum = (int)$vatNumber[8];
		$weights = [9, 8, 7, 6, 5, 4, 3, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = (11 - ($checkVal % 11)) > 9 ? 0 : (11 - ($checkVal % 11));

		return $checksum == $checkVal;
	}
}
