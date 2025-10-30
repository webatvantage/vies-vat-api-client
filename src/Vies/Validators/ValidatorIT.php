<?php

namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorIT
 *
 */
class ValidatorIT extends VatValidator
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

		if (substr($vatNumber, 0, 7) == '0000000')
		{
			return false;
		}

		$checksum = (int) substr($vatNumber, -1);
		$Sum1 = 0;
		$Sum2 = 0;
		for ($i = 1; $i <= 10; $i++)
		{
			if (!$this->isEven($i))
			{
				$Sum1 += $vatNumber[$i - 1];
			}
			else
			{
				$Sum2 += (int)($vatNumber[$i - 1] / 5) + ((2 * $vatNumber[$i - 1]) % 10);
			}
		}

		$checkVal = (10 - ($Sum1 + $Sum2) % 10) % 10;

		return $checksum == $checkVal;
	}
}
