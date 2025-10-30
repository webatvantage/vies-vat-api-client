<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorMT
 *
 */
class ValidatorMT extends VatValidator
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

		if ((int)substr($vatNumber, 0, 6) <= 100000)
		{
			return false;
		}

		$weights = [3, 4, 6, 7, 8, 9];
		$checksum = (int)substr($vatNumber, -2, 2);
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal = intval(37 - ($checkVal % 37));

		return $checkVal == $checksum;
	}
}
