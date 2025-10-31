<?php

namespace Webatvantage\Vies;

use DateTime;
use Webatvantage\Vies\Exceptions\InvalidCountryCodeException;

class Countries
{
	public const VIES_EU_COUNTRY_TOTAL = 28;

	/** @var array<string,Country> */
	protected static array $countries;

	/** @var array<string,ExcludedCountry> */
	protected static array $excludedCountries;

	/**
	 * @return array<string,Country>
	 */
	public static function countries(): array
	{
		if (isset(static::$countries))
		{
			return static::$countries;
		}

		static::$countries = [
			'AT' => new Country('AT', 'Austria', Validators\ValidatorAT::class, ['U99999999']),
			'BE' => new Country('BE', 'Belgium', Validators\ValidatorBE::class, ['09999999999', '19999999999']),
			'BG' => new Country('BG', 'Bulgaria', Validators\ValidatorBG::class, ['999999999', '9999999999']),
			'CY' => new Country('CY', 'Cyprus', Validators\ValidatorCY::class, ['999999999L']),
			'CZ' => new Country('CZ', 'Czech Republic', Validators\ValidatorCZ::class, ['99999999', '999999999', '9999999999']),
			'DE' => new Country('DE', 'Germany', Validators\ValidatorDE::class, ['999999999']),
			'DK' => new Country('DK', 'Denmark', Validators\ValidatorDK::class, ['99999999']),
			'EE' => new Country('EE', 'Estonia', Validators\ValidatorEE::class, ['999999999']),
			'EL' => new Country('EL', 'Greece', Validators\ValidatorEL::class, ['999999999']),
			'ES' => new Country('ES', 'Spain', Validators\ValidatorES::class, ['L99999999', '99999999L', 'L9999999L']),
			'FI' => new Country('FI', 'Finland', Validators\ValidatorFI::class, ['FI99999999']),
			'FR' => new Country('FR', 'France', Validators\ValidatorFR::class, ['XX 999999999']),
			'HR' => new Country('HR', 'Croatia', Validators\ValidatorHR::class, ['99999999999']),
			'HU' => new Country('HU', 'Hungary', Validators\ValidatorHU::class, ['99999999']),
			'IE' => new Country('IE', 'Ireland', Validators\ValidatorIE::class, ['9S99999L', '9999999WI']),
			'IT' => new Country('IT', 'Italy', Validators\ValidatorIT::class, ['99999999999']),
			'LU' => new Country('LU', 'Luxembourg', Validators\ValidatorLU::class, ['99999999']),
			'LV' => new Country('LV', 'Latvia', Validators\ValidatorLV::class, ['99999999999']),
			'LT' => new Country('LT', 'Lithuania', Validators\ValidatorLT::class, ['999999999', '999999999999']),
			'MT' => new Country('MT', 'Malta', Validators\ValidatorMT::class, ['99999999']),
			'NL' => new Country('NL', 'Netherlands', Validators\ValidatorNL::class, ['999999999B01']),
			'PL' => new Country('PL', 'Poland', Validators\ValidatorPL::class, ['9999999999']),
			'PT' => new Country('PT', 'Portugal', Validators\ValidatorPT::class, ['999999999']),
			'RO' => new Country('RO', 'Romania', Validators\ValidatorRO::class, ['99********']),
			'SE' => new Country('SE', 'Sweden', Validators\ValidatorSE::class, ['999999999999']),
			'SI' => new Country('SI', 'Slovenia', Validators\ValidatorSI::class, ['99999999']),
			'SK' => new Country('SK', 'Slovakia', Validators\ValidatorSK::class, ['9999999999']),
			'GB' => new Country('GB', 'United Kingdom', Validators\ValidatorGB::class, ['999999999']),
			'XI' => new Country('XI', 'United Kingdom (Northern Ireland)', Validators\ValidatorXI::class, ['999999999']),
			'EU' => new Country('EU', 'MOSS Number', Validators\ValidatorEU::class, []),
		];

		return static::$countries;
	}

	/**
	 * @return array<string,ExcludedCountry>
	 */
	public static function excludedCountries(): array
	{
		if (isset(static::$excludedCountries))
		{
			return static::$excludedCountries;
		}

		static::$excludedCountries = [
			'GB' => new ExcludedCountry('GB', 'United Kingdom', DateTime::createFromFormat('!Y-m-d', '2021-01-01'), 'Brexit'),
		];

		return static::$excludedCountries;
	}

	/**
	 * A list of European Union countries as of January 2015
	 *
	 * @return array<string,Country>
	 */
	public static function europeanCountries(): array
	{
		static $list;

		if ($list)
		{
			return $list;
		}

		$countries = static::countries();

		$list = array_combine(
			array_keys($countries),
			array_map(fn (Country $country) => $country->getName(), $countries),
		);

		unset($list['EU']); // Remove MOSS Number

		foreach (array_keys(static::excludedCountries()) as $excludedCountryCode)
		{
			unset($list[$excludedCountryCode]);
		}

		return $list;
	}

	/**
	 * @throws InvalidCountryCodeException
	 */
	public static function country(string $countryCode): Country
	{
		if (!static::hasCountry($countryCode))
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		return static::$countries[$countryCode];
	}

	public static function hasCountry(string $countryCode): bool
	{
		return array_key_exists($countryCode, static::countries());
	}

	public static function isExcluded(string $countryCode): bool
	{
		return array_key_exists($countryCode, static::excludedCountries());
	}

	/**
	 * @throws InvalidCountryCodeException
	 */
	public static function excludedCountry(string $countryCode): ExcludedCountry
	{
		if (!static::isExcluded($countryCode))
		{
			throw new InvalidCountryCodeException($countryCode);
		}

		return static::$excludedCountries[$countryCode];
	}
}
