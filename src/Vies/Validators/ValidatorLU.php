<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorLU
 *
 */
class ValidatorLU extends VatValidator
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

		$checksum = (int)substr($vatNumber, -2);
		$checkVal = (int)substr($vatNumber, 0, 6);

		return ($checkVal % 89) == $checksum;
	}
}
