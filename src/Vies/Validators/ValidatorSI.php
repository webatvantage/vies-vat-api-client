<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorSI
 *
 */
class ValidatorSI extends VatValidator
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

		if (intval($vatNumber[0]) == 0)
		{
			return false;
		}

		$checksum = (int)$vatNumber[7];
		$weights = [8, 7, 6, 5, 4, 3, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);

		$mod = 11 - ($checkVal % 11);

		if ($mod === 11)
		{
			return false;
		}

		$checkVal = ($mod == 10) ? 0 : $mod;

		return $checksum == $checkVal;
	}
}
