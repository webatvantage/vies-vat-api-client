<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorHR
 *
 */
class ValidatorHR extends VatValidator
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

		if (!ctype_digit($vatNumber))
		{
			return false;
		}

		$product = 10;

		for ($i = 0; $i < 10; $i++)
		{
			$sum = ($vatNumber[$i] + $product) % 10;
			$sum = ($sum == 0) ? 10 : $sum;
			$product = (2 * $sum) % 11;
		}

		return ($product + (int) $vatNumber[10]) % 10 == 1;
	}
}
