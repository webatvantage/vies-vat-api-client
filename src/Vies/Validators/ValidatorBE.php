<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorBE
 *
 */
class ValidatorBE extends VatValidator
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

		$checkVal = (int) substr($vatNumber, 0, -2);

		return 97 - ($checkVal % 97) == (int) substr($vatNumber, -2);
	}
}
