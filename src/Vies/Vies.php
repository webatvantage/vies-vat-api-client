<?php

/**
 * Vies
 *
 * Component using the European Commission (EC) VAT Information Exchange System (VIES) to verify and validate VAT
 * registration numbers in the EU, using PHP and Composer.
 *
 * @license MIT
 */

namespace Webatvantage\Vies;

use Webatvantage\Vies\Api\VatEuropeApi;
use Webatvantage\Vies\Api\VatResponse;
use Webatvantage\Vies\Exceptions\CountryNoLongerSupportedException;
use Webatvantage\Vies\Exceptions\InvalidCountryCodeException;
use Webatvantage\Vies\Exceptions\InvalidVatNumberFormatException;
use Webatvantage\Vies\Exceptions\ViesException;
use Webatvantage\Vies\Exceptions\ViesServiceException;

/**
 * Class Vies
 *
 * This class provides a soap client for usage of the VIES web service
 * provided by the European Commission to validate VAT numbers of companies
 * registered within the European Union
 */
class Vies
{
	protected VatEuropeApi $api;

	public function __construct(VatEuropeApi $api)
	{
		$this->api = $api;
	}

	/**
	 * Validates a given country code and VAT number
	 *
	 * @param string $countryCode The two-character country code of a European member country
	 * @param string $vatNumber The VAT number (without the country identification) of a registered company
	 *
	 * @throws ViesException
	 * @throws ViesServiceException
	 */
	public function validateVat(string $countryCode, string $vatNumber): VatResponse
	{
		$this->validateVatFormat($countryCode, $vatNumber);

		if (Countries::isExcluded($countryCode))
		{
			$country = Countries::excludedCountry($countryCode);

			throw new CountryNoLongerSupportedException($country->getName(), $country->getExcluded()->format('Y-m-d'), $country->getReason());
		}

		return $this->api->retrieveVatInstance($countryCode, $vatNumber);
	}

	/**
	 * Validate the format of the VAT number and throw exceptions on failures
	 *
	 * @throws ViesException
	 * @throws InvalidCountryCodeException
	 * @throws InvalidVatNumberFormatException
	 */
	public function validateVatFormat(string $countryCode, string $vatNumber, bool $useExcludedCountries = false): bool
	{
		if ($this->validateCountryCode($countryCode, $useExcludedCountries) === false)
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		$vatNumber = static::normalizeVat($vatNumber);

		if (!$this->validateVatSum($countryCode, $vatNumber, $useExcludedCountries))
		{
			throw new InvalidVatNumberFormatException(
				$countryCode,
				$vatNumber,
			);
		}

		return true;
	}

	/**
	 * Validate a VAT number control sum
	 *
	 * @param string $countryCode The two-character country code of a European member country
	 * @param string $vatNumber The VAT number (without the country identification) of a registered company
	 *
	 * @throws ViesException
	 */
	public function validateVatSum(string $countryCode, string $vatNumber, bool $useExcludedCountries = false): bool
	{
		if ($this->validateCountryCode($countryCode, $useExcludedCountries) === false)
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		return Countries::country($countryCode)->getValidator()->validate(static::normalizeVat($vatNumber));
	}

	/**
	 * Check if the country code is listed in the EU country list
	 *
	 * @param string $countryCode
	 * @param bool $useExcludedCountries
	 *
	 * @return bool
	 */
	public function validateCountryCode(string $countryCode, bool $useExcludedCountries = false): bool
	{
		if (Countries::hasCountry($countryCode) === false)
		{
			return false;
		}

		if ($useExcludedCountries === false && Countries::isExcluded($countryCode) === true)
		{
			return false;
		}

		return true;
	}

	/**
	 * Filters a VAT number and normalizes it to an alphanumeric string
	 *
	 * @param string $vatNumber
	 *
	 * @return string
	 *
	 * @static
	 */
	public static function normalizeVat(string $vatNumber): string
	{
		$vatNumber = mb_trim($vatNumber);
		$vatNumber = mb_strtoupper($vatNumber);

		return str_replace([' ', '.', '-'], '', $vatNumber);
	}

	/**
	 * Splits a VAT ID on country code and VAT number
	 *
	 * @param string $vatId
	 *
	 * @return array{country: string, id: string}
	 */
	public static function splitVatId(string $vatId): array
	{
		return [
			'country' => substr($vatId, 0, 2),
			'id' => substr($vatId, 2),
		];
	}
}
