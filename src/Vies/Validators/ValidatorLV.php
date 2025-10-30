<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorLV
 *
 */
class ValidatorLV extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 11)
		{
			return false;
		}

		if ((int)$vatNumber[0] <= 3)
		{
			return false;
		}

		$weights = [9, 1, 4, 8, 3, 10, 2, 5, 7, 6];
		$checksum = (int)substr($vatNumber, -1);
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = 3 - ($checkVal % 11);

		if ($checkVal == -1)
		{
			return false;
		}

		if ($checkVal < -1)
		{
			$checkVal += 11;
		}

		return $checksum == $checkVal;
	}
}
