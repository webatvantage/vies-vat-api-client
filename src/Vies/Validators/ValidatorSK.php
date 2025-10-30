<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorSK
 *
 */
class ValidatorSK extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 10
			|| intval($vatNumber[0]) == 0
			|| !in_array((int) $vatNumber[2], [2, 3, 4, 7, 8, 9])
		) {
			return false;
		}

		if (PHP_INT_SIZE === 4 && extension_loaded("bcmath"))
		{
			return bcmod($vatNumber, '11') === '0';
		}
		else
		{
			return $vatNumber % 11 == 0;
		}
	}
}
