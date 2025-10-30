<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorDE
 *
 */
class ValidatorDE extends VatValidator
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

		$prod = 10;
		for ($i = 0; $i < 8; $i++)
		{
			$checkVal = ((int)$vatNumber[$i] + $prod) % 10;
			$checkVal = ($checkVal == 0) ? 10 : $checkVal;
			$prod = ($checkVal * 2) % 11;
		}

		$prod = $prod == 1 ? 11 : $prod;

		return 11 - $prod == (int) substr($vatNumber, -1);
	}
}
