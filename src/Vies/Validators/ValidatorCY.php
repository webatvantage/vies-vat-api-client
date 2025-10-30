<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorCY
 *
 */
class ValidatorCY extends VatValidator
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

		if (intval(substr($vatNumber, 0, 2) == 12))
		{
			return false;
		}

		return in_array((int) $vatNumber[0], [0, 1, 3, 4, 5, 6, 9], true)
			&& ctype_alpha($vatNumber[8]);
	}
}
