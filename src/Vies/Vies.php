<?php

declare (strict_types=1);

/**
 * Vies
 *
 * Component using the European Commission (EC) VAT Information Exchange System (VIES) to verify and validate VAT
 * registration numbers in the EU, using PHP and Composer.
 *
 * @author Michelangelo van Dam <dragonbe+github@gmail.com>
 * @author Tom Six <tom@webatvantage.be>
 * @license  MIT
 *
 */
namespace DragonBe\Vies;

use DragonBe\Vies\Exceptions\CountryNoLongerSupportedException;
use DragonBe\Vies\Exceptions\InvalidCountryCodeException;
use DragonBe\Vies\Exceptions\InvalidVatNumberFormatException;
use DragonBe\Vies\Exceptions\ViesException;
use DragonBe\Vies\Exceptions\ViesServiceException;

/**
 * Class Vies
 *
 * This class provides a soap client for usage of the VIES web service
 * provided by the European Commission to validate VAT numbers of companies
 * registered within the European Union
 *
 * @category DragonBe
 * @package \DragonBe\Vies
 */
class Vies
{
    const VIES_EU_COUNTRY_TOTAL = 28;

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

    protected const VIES_EXCLUDED_COUNTRY_CODES = [
        'GB' => ['name' => 'United Kingdom', 'excluded' => '2021-01-01', 'reason' => 'Brexit'],
    ];

    protected VatEuropeApi $api;

    public function __construct(VatEuropeApi $api)
    {
        $this->api = $api;
    }

    /**
     * Sets the heartbeat functionality to verify if the VIES service is alive or not,
     * especially since this service tends to have a bad reputation of its availability.
     *
     * @param HeartBeat $heartBeat
     * @return static
     */
    public function setHeartBeat(HeartBeat $heartBeat): self
    {
        $this->heartBeat = $heartBeat;

        return $this;
    }

    /**
     * Validates a given country code and VAT number
     *
     * @param string $countryCode The two-character country code of a European member country
     * @param string $vatNumber The VAT number (without the country identification) of a registered company
     * @throws ViesException
     * @throws ViesServiceException
     */
    public function validateVat(string $countryCode, string $vatNumber): VatInstance
    {
        if ($this->validateCountryCode($countryCode, true) === false) {
            throw new InvalidCountryCodeException($countryCode);
        }

        $vatNumber = static::normalizeVat($vatNumber);

        if (! $this->validateVatSum($countryCode, $vatNumber)) {
            throw new InvalidVatNumberFormatException(
                $countryCode,
                $vatNumber,
            );
        }

        if (array_key_exists($countryCode, static::VIES_EXCLUDED_COUNTRY_CODES)) {
            throw new CountryNoLongerSupportedException(
                static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['name'],
                static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['excluded'],
                static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode]['reason'],
            );
        }

        return $this->api->retrieveVatInstance($countryCode, $vatNumber);
    }

    /**
     * Validate a VAT number control sum
     *
     * @param string $countryCode The two-character country code of a European member country
     * @param string $vatNumber The VAT number (without the country identification) of a registered company
     * @return bool
     * @throws ViesException
     */
    public function validateVatSum(string $countryCode, string $vatNumber): bool
    {
        if ($this->validateCountryCode($countryCode, true) === false) {
            throw new InvalidCountryCodeException($countryCode);
        }

        /**
         * @var class-string<VatValidator> $className
         */
        $className = static::VIES_EU_COUNTRY_LIST[$countryCode]['validator'];

        return (new $className())->validate(static::normalizeVat($vatNumber));
    }

    /**
     * @param string $countryCode
     * @param bool $useExcludedCountries
     *
     * @return bool
     */
    public function validateCountryCode(string $countryCode, bool $useExcludedCountries = false): bool
    {
        if (! isset(static::VIES_EU_COUNTRY_LIST[$countryCode])) {
            return false;
        }
        if ($useExcludedCountries === false && isset(static::VIES_EXCLUDED_COUNTRY_CODES[$countryCode])) {
            return false;
        }

        return true;
    }

    /**
     * Filters a VAT number and normalizes it to an alphanumeric string
     *
     * @param string $vatNumber
     * @return string
     * @static
     */
    public static function normalizeVat(string $vatNumber): string
    {
        return str_replace([' ', '.', '-'], '', $vatNumber);
    }

    /**
     * Splits a VAT ID on country code and VAT number
     *
     * @param string $vatId
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
     * @return array
     */
    public static function listEuropeanCountries(): array
    {
        static $list;

        if ($list) {
            return $list;

        }

        $list = array_combine(
            array_keys(static::VIES_EU_COUNTRY_LIST),
            array_column(static::VIES_EU_COUNTRY_LIST, 'name'),
        );

        unset($list['EU']);

        foreach (array_keys(static::VIES_EXCLUDED_COUNTRY_CODES) as $excludedCountryCode) {
            unset($list[$excludedCountryCode]);
        }

        return $list;
    }
}
