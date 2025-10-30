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
 *
 * @category DragonBe
 */
class Vies
{
	public const VIES_EU_COUNTRY_TOTAL = 28;

	/** @var array<string,array{name: string, validator: VatValidator}> */
	protected const VIES_EU_COUNTRY_LIST = [
		'AT' => ['name' => 'Austria', 'validator' => Validators\ValidatorAT::class],
		'BE' => ['name' => 'Belgium', 'validator' => Validators\ValidatorBE::class],
		'BG' => ['name' => 'Bulgaria', 'validator' => Validators\ValidatorBG::class],
		'CY' => ['name' => 'Cyprus', 'validator' => Validators\ValidatorCY::class],
		'CZ' => ['name' => 'Czech Republic', 'validator' => Validators\ValidatorCZ::class],
		'DE' => ['name' => 'Germany', 'validator' => Validators\ValidatorDE::class],
		'DK' => ['name' => 'Denmark', 'validator' => Validators\ValidatorDK::class],
		'EE' => ['name' => 'Estonia', 'validator' => Validators\ValidatorEE::class],
		'EL' => ['name' => 'Greece', 'validator' => Validators\ValidatorEL::class],
		'ES' => ['name' => 'Spain', 'validator' => Validators\ValidatorES::class],
		'FI' => ['name' => 'Finland', 'validator' => Validators\ValidatorFI::class],
		'FR' => ['name' => 'France', 'validator' => Validators\ValidatorFR::class],
		'HR' => ['name' => 'Croatia', 'validator' => Validators\ValidatorHR::class],
		'HU' => ['name' => 'Hungary', 'validator' => Validators\ValidatorHU::class],
		'IE' => ['name' => 'Ireland', 'validator' => Validators\ValidatorIE::class],
		'IT' => ['name' => 'Italy', 'validator' => Validators\ValidatorIT::class],
		'LU' => ['name' => 'Luxembourg', 'validator' => Validators\ValidatorLU::class],
		'LV' => ['name' => 'Latvia', 'validator' => Validators\ValidatorLV::class],
		'LT' => ['name' => 'Lithuania', 'validator' => Validators\ValidatorLT::class],
		'MT' => ['name' => 'Malta', 'validator' => Validators\ValidatorMT::class],
		'NL' => ['name' => 'Netherlands', 'validator' => Validators\ValidatorNL::class],
		'PL' => ['name' => 'Poland', 'validator' => Validators\ValidatorPL::class],
		'PT' => ['name' => 'Portugal', 'validator' => Validators\ValidatorPT::class],
		'RO' => ['name' => 'Romania', 'validator' => Validators\ValidatorRO::class],
		'SE' => ['name' => 'Sweden', 'validator' => Validators\ValidatorSE::class],
		'SI' => ['name' => 'Slovenia', 'validator' => Validators\ValidatorSI::class],
		'SK' => ['name' => 'Slovakia', 'validator' => Validators\ValidatorSK::class],
		'GB' => ['name' => 'United Kingdom', 'validator' => Validators\ValidatorGB::class],
		'XI' => ['name' => 'United Kingdom (Northern Ireland)', 'validator' => Validators\ValidatorXI::class],
		'EU' => ['name' => 'MOSS Number', 'validator' => Validators\ValidatorEU::class],
	];

	/** @var array<string,array{name: string, excluded: string, reason: string}> */
	protected const VIES_EXCLUDED_COUNTRY_CODES = [
		'GB' => ['name' => 'United Kingdom', 'excluded' => '2021-01-01', 'reason' => 'Brexit'],
	];

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

		if (array_key_exists($countryCode, static::VIES_EXCLUDED_COUNTRY_CODES))
		{
			throw new CountryNoLongerSupportedException(
				static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['name'],
				static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['excluded'],
				static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['reason'],
			);
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
	public function validateVatFormat(string $countryCode, string $vatNumber): bool
	{
		if ($this->validateCountryCode($countryCode, true) === false)
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		$vatNumber = static::normalizeVat($vatNumber);

		if (!$this->validateVatSum($countryCode, $vatNumber))
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
	public function validateVatSum(string $countryCode, string $vatNumber): bool
	{
		if ($this->validateCountryCode($countryCode, true) === false)
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		/** @var class-string<VatValidator> $className */
		$className = static::VIES_EU_COUNTRY_LIST[$countryCode]['validator'];

		return (new $className())->validate(static::normalizeVat($vatNumber));
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
		if (!isset(static::VIES_EU_COUNTRY_LIST[$countryCode]))
		{
			return false;
		}

		if ($useExcludedCountries === false && isset(static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]))
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

	/**
	 * A list of European Union countries as of January 2015
	 *
	 * @return array<string,array{name: string, validator: VatValidator}>
	 */
	public static function listEuropeanCountries(): array
	{
		static $list;

		if ($list)
		{
			return $list;

		}

		$list = array_combine(
			array_keys(static::VIES_EU_COUNTRY_LIST),
			array_column(static::VIES_EU_COUNTRY_LIST, 'name'),
		);

		unset($list['EU']);

		foreach (array_keys(static::VIES_EXCLUDED_COUNTRY_CODES) as $excludedCountryCode)
		{
			unset($list[$excludedCountryCode]);
		}

		return $list;
	}
}
