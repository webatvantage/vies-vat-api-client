<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorPL
 *
 */
class ValidatorPL extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 10)
		{
			return false;
		}

		$weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
		$checksum = (int)$vatNumber[9];
		$checkVal = $this->sumWeights($weights, $vatNumber);

		return $checkVal % 11 == $checksum;
	}
}
