<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * This validator was derived from the "ValidatorGB" validator to validate the "XI" VAT numbers of
 * "United Kingdom (Northern Ireland)" following the end of the Brexit transition period on December, 31 2020.
 *
 * It is based on the information at:
 * https://www.avalara.com/vatlive/en/vat-news/brexit-northern-ireland-vat-and-eoro--xi--number.html
 *
 * As more information is published, this validator may be made more precise:
 *
 * - Remove the logic to validate Isle of Man numbers?
 * - Remove the logic to validate 'GD' and 'HA' prefixes?
 *
 */

/**
 * Class ValidatorXI
 *
 */
class ValidatorXI extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) == 5)
		{
			return $this->validateGovernment($vatNumber);
		}

		if (strlen($vatNumber) != 9 && strlen($vatNumber) != 12)
		{
			return false;
		}

		$weights = [8, 7, 6, 5, 4, 3, 2];
		$checkVal = $this->sumWeights($weights, $vatNumber);
		$checkVal += (int)substr($vatNumber, 7, 2);

		$Result1 = $checkVal % 97;
		$Result2 = ($Result1 + 55) % 97;

		return !($Result1 * $Result2);
	}

	/**
	 * Validate Government VAT
	 *
	 * @param  string $vatNumber
	 *
	 * @return bool
	 */
	private function validateGovernment(string $vatNumber): bool
	{
		$prefix = strtoupper(substr($vatNumber, 0, 2));
		$number = (int) substr($vatNumber, 2, 3);

		// Government departments
		if ($prefix == 'GD')
		{
			return $number < 500;
		}

		// Health authorities
		if ($prefix == 'HA')
		{
			return $number > 499;
		}

		return false;
	}
}
