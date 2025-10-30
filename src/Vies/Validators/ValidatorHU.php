<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorHU
 *
 */
class ValidatorHU extends VatValidator
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

		$weights = [9, 7, 3, 1, 9, 7, 3];
		$checksum = (int) $vatNumber[7];
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = (int) substr((string) $checkVal, -1);
		$checkVal = ($checkVal > 0) ? 10 - $checkVal : 0;

		return $checksum == $checkVal;
	}
}
