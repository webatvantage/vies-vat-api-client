<?php


namespace Webatvantage\Vies\Validators;

use Webatvantage\Vies\VatValidator;

/**
 * Class ValidatorES
 *
 */
class ValidatorES extends VatValidator
{
	/**
	 * Allowed C1 if C9 is Alphabetic
	 *
	 * @var array
	 */
	protected $allowedC1Alphabetic = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'N', 'P', 'Q', 'R', 'S', 'W'];

	/**
	 * Allowed C1 if C9 is Numeric for National juridical
	 *
	 * @var array
	 */
	protected $allowedC1Numeric = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'U', 'V'];

	/**
	 * Allowed C1 if C9 is Numeric for physical person
	 *
	 * @var array
	 */
	protected $allowedC1Physical = ['K', 'L', 'M', 'X', 'Y', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

	/**
	 * Check Character: 1-A, 2-B, 3-C, 4-D, 5-E, 6-F, 7-G, 8-H, 9-I, 10-J
	 *
	 * @var array
	 */
	protected $checkCharacter = [
		1 => 'A',
		2 => 'B',
		3 => 'C',
		4 => 'D',
		5 => 'E',
		6 => 'F',
		7 => 'G',
		8 => 'H',
		9 => 'I',
		10 => 'J',
	];

	/**
	 * Check Character: 1-T, 2-R, 3-W, 4-A, 5-G, 6-M, 7-Y, 8-F, 9-P, 10-D, 11-X, 12-B, 13-N,
	 *                  14-J, 15-Z, 16-S, 17-Q, 18-V, 19-H, 20-L, 21-C, 22-K, 23-E
	 *
	 * @var array
	 */
	protected $checkCharacterPhysical = [
		1 => 'T',
		2 => 'R',
		3 => 'W',
		4 => 'A',
		5 => 'G',
		6 => 'M',
		7 => 'Y',
		8 => 'F',
		9 => 'P',
		10 => 'D',
		11 => 'X',
		12 => 'B',
		13 => 'N',
		14 => 'J',
		15 => 'Z',
		16 => 'S',
		17 => 'Q',
		18 => 'V',
		19 => 'H',
		20 => 'L',
		21 => 'C',
		22 => 'K',
		23 => 'E',
	];

	/**
	 * {@inheritdoc}
	 */
	public function validate(string $vatNumber): bool
	{
		if (strlen($vatNumber) != 9)
		{
			return false;
		}

		if (!is_numeric(substr($vatNumber, 1, 6)))
		{
			return false;
		}

		$checksum = $vatNumber[8];
		$fieldC1 = $vatNumber[0];

		// Juridical entities other than national ones
		if (ctype_alpha($checksum) && in_array($fieldC1, $this->allowedC1Alphabetic))
		{
			return $checksum === $this->validateJuridical($vatNumber);
		}

		// Physical person
		if (ctype_alpha($checksum) && in_array($fieldC1, $this->allowedC1Physical))
		{
			return $checksum === $this->validatePhysical($vatNumber);
		}

		// National juridical entities
		if (ctype_digit($checksum) && in_array($fieldC1, $this->allowedC1Numeric))
		{
			return (int) $checksum === $this->validateNational($vatNumber);
		}

		return false;
	}

	/**
	 * @param string $vatNumber
	 *
	 * @return string
	 */
	private function validateJuridical(string $vatNumber): string
	{
		$checkVal = 0;

		for ($i = 2; $i <= 8; $i++)
		{
			$checkVal += $this->crossSum((int)$vatNumber[9 - $i] * ($this->isEven($i) ? 2 : 1));
		}

		$checkVal = 10 - ($checkVal % 10);

		return $this->checkCharacter[$checkVal];
	}

	/**
	 * @param string $vatNumber
	 *
	 * @return int
	 */
	private function validateNational(string $vatNumber): int
	{
		$checkVal = 0;

		for ($i = 2; $i <= 8; $i++)
		{
			$checkVal += $this->crossSum((int)$vatNumber[9 - $i] * ($this->isEven($i) ? 2 : 1));
		}

		$checkVal = 10 - ($checkVal % 10);

		return $checkVal % 10;
	}

	/**
	 * @param string $vatNumber
	 *
	 * @return string
	 */
	private function validatePhysical(string $vatNumber): string
	{
		$vatNumber[0] = str_replace(['Y', 'Z'], [1, 2], $vatNumber[0]);

		if (ctype_digit($vatNumber[0]))
		{
			$checkVal = ((int)substr($vatNumber, 0, 8) % 23) + 1;
		}
		else
		{
			$checkVal = ((int)substr($vatNumber, 1, 7) % 23) + 1;
		}

		return $this->checkCharacterPhysical[$checkVal];
	}
}
