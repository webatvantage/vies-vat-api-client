<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorAT
 *
 */
class ValidatorAT extends VatValidator
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

		if (strtoupper($vatNumber[0]) != 'U')
		{
			return false;
		}

		$checkVal = 0;
		for ($i = 1; $i < 8; $i++)
		{
			$checkVal += $this->crossSum((int)$vatNumber[$i] * ($this->isEven($i) ? 2 : 1));
		}

		$checkVal = substr((string)(96 - $checkVal), -1);

		return (int)$vatNumber[8] == $checkVal;
	}
}
