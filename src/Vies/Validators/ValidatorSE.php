<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorSE
 *
 */
class ValidatorSE extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 12)
		{
			return false;
		}

		if ((int)substr($vatNumber, -2) < 1 || (int)substr($vatNumber, -2) > 94)
		{
			return false;
		}

		$checksum = (int)$vatNumber[9];
		$checkVal = 0;

		for ($i = 1; $i < 10; $i++)
		{
			$checkVal += $this->crossSum((int)$vatNumber[9 - $i] * ($this->isEven($i) ? 1 : 2));
		}

		return $checksum == (($checkVal % 10) == 0 ? 0 : 10 - ($checkVal % 10));
	}
}
