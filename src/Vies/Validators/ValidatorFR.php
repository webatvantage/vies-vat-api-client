<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorFR
 *
 */
class ValidatorFR extends VatValidator
{
	/**
	 * the valid characters for the first two digits (O and I are missing)
	 *
	 * @var string
	 */
	protected $alphabet = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';

	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 11)
		{
			return false;
		}

		if (strpos($this->alphabet, $vatNumber[0]) === false)
		{
			return false;
		}

		if (strpos($this->alphabet, $vatNumber[1]) === false)
		{
			return false;
		}

		$checksum = substr($vatNumber, 0, 2);

		if (ctype_digit($checksum))
		{
			return $checksum == $this->validateOld($vatNumber);
		}

		return $checksum == $this->validateNew($vatNumber);
	}

	/**
	 * @param string $vatNumber
	 *
	 * @return string
	 */
	private function validateOld(string $vatNumber): string
	{
		$checkVal = substr($vatNumber, 2);

		if (!ctype_digit($checkVal))
		{
			return "";
		}
		$checkVal .= "12";

		if (PHP_INT_SIZE === 4 && extension_loaded('bcmath'))
		{
			$checkVal = (int) bcmod($checkVal, "97");
		}
		else
		{
			$checkVal = intval($checkVal) % 97;
		}

		return $checkVal == 0 ? "00" : (string) $checkVal;
	}

	/**
	 * @param string $vatNumber
	 *
	 * @return bool
	 */
	private function validateNew(string $vatNumber): bool
	{
		$multiplier = 34;
		$subStractor = 100;

		if (ctype_digit($vatNumber[0]))
		{
			$multiplier = 24;
			$subStractor = 10;
		}

		$checkCharacter = array_flip(str_split($this->alphabet));
		$checkVal = ($checkCharacter[$vatNumber[0]] * $multiplier) + $checkCharacter[$vatNumber[1]] - $subStractor;

		if (PHP_INT_SIZE === 4 && extension_loaded("bcmath"))
		{
			return (int) bcmod(bcadd(substr($vatNumber, 2), strval(($checkVal / 11) + 1)), "11") === $checkVal % 11;
		}
		else
		{
			return ((int) (intval(substr($vatNumber, 2)) + ($checkVal / 11) + 1) % 11) == $checkVal % 11;
		}
	}
}
