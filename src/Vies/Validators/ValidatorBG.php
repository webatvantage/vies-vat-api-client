<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorBG
 *
 */
class ValidatorBG extends VatValidator
{
	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		$vatNumberLength = strlen($vatNumber);

		if (!in_array($vatNumberLength, [9, 10], true))
		{
			return false;
		}

		if (10 === $vatNumberLength)
		{
			return $this->validateNaturalPerson($vatNumber)
				|| $this->validateForeignNaturalPerson($vatNumber);
		}

		return $this->validateBusiness($vatNumber);
	}

	/**
	 * Validation for business VAT ID's with 9 digits
	 *
	 * @param string $vatNumber
	 *
	 * @return bool
	 */
	private function validateBusiness(string $vatNumber): bool
	{
		$weights = [1, 2, 3, 4, 5, 6, 7, 8];

		return $this->checkValue($vatNumber, $weights, parent::DEFAULT_MODULO, 8);
	}

	/**
	 * Validate VAT ID's for natural persons
	 *
	 * @param string $vatNumber
	 *
	 * @return bool
	 *
	 * @see https://github.com/yolk/valvat/blob/master/lib/valvat/checksum/bg.rb
	 */
	private function validateNaturalPerson(string $vatNumber): bool
	{
		$weights = [2, 4, 8, 5, 10, 9, 7, 3, 6];

		return $this->checkValue($vatNumber, $weights, parent::DEFAULT_MODULO, parent::DEFAULT_VAT_POSITION);
	}

	/**
	 * Validate VAT ID's for foreign natural persons
	 *
	 * @param string $vatNumber
	 *
	 * @return bool
	 *
	 * @see https://github.com/yolk/valvat/blob/master/lib/valvat/checksum/bg.rb
	 */
	private function validateForeignNaturalPerson(string $vatNumber): bool
	{
		$weights = [21, 19, 17, 13, 11, 9, 7, 3, 1];

		return $this->checkValue($vatNumber, $weights, 10, parent::DEFAULT_VAT_POSITION);
	}
}
