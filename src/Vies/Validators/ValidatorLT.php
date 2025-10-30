<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorLT
 *
 */
class ValidatorLT extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) == 12)
		{
			return $this->validateTemporaryTaxpayer($vatNumber);
		}

		if (strlen($vatNumber) == 9)
		{
			return $this->validateLegal($vatNumber);
		}

		return false;
	}

	/**
	 * Validate Temporary Tax Payer
	 *
	 * @param string $vatNumber
	 *
	 * @return bool
	 */
	private function validateTemporaryTaxpayer(string $vatNumber): bool
	{
		if ($vatNumber[10] != 1)
		{
			return false;
		}

		$weights = [1, 2, 3, 4, 5, 6, 7, 8, 9, 1, 2];
		$checksum = (int)$vatNumber[11];
		$checkVal = $this->sumWeights($weights, $vatNumber);

		if (($checkVal % 11) == 10)
		{
			$weights = [3, 4, 5, 6, 7, 8, 9, 1, 2, 3, 4];
			$checkVal = $this->sumWeights($weights, $vatNumber);
			$checkVal = ($checkVal % 11 == 10) ? 0 : $checkVal % 11;

			return $checkVal == $checksum;
		}

		return $checkVal % 11 == $checksum;
	}

	/**
	 * Validate Legal
	 *
	 * @param string $vatNumber
	 *
	 * @return bool
	 */
	private function validateLegal(string $vatNumber): bool
	{
		if ($vatNumber[7] != 1)
		{
			return false;
		}

		$weights = [1, 2, 3, 4, 5, 6, 7, 8];
		$checksum = (int) $vatNumber[8];
		$checkVal = $this->sumWeights($weights, $vatNumber);

		if (($checkVal % 11) == 10)
		{
			$weights = [3, 4, 5, 6, 7, 8, 9, 1];
			$checkVal = $this->sumWeights($weights, $vatNumber);
			$checkVal = ($checkVal % 11 == 10) ? 0 : $checkVal % 11;

			return $checkVal == $checksum;
		}

		return $checkVal % 11 == $checksum;
	}
}
