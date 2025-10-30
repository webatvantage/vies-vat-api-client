<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorRO
 *
 */
class ValidatorRO extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) < 2 || strlen($vatNumber) > 10)
		{
			return false;
		}

		$vatNumber = str_pad($vatNumber, 10, "0", STR_PAD_LEFT);
		$checksum = (int)$vatNumber[9];
		$weights = [7, 5, 3, 2, 1, 7, 5, 3, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = ($checkVal * 10) % 11;

		if ($checkVal == 10)
		{
			$checkVal = 0;
		}

		return $checkVal == $checksum;
	}
}
