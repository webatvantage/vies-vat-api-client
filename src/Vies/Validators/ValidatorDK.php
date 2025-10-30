<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorDK
 *
 */
class ValidatorDK extends VatValidator
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

		$checksum = $this->sumWeights([2, 7, 6, 5, 4, 3, 2, 1], $vatNumber);

		return ($checksum % 11) === 0;
	}
}
