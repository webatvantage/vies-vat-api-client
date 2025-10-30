# VIES

Component using the European Commission (EC) VAT Information Exchange System (VIES) to verify and validate VAT registration numbers in the EU, using PHP and Composer.

The `Vies` class provides functionality to make a API call to VIES and returns an object `VatResponse` containing the following information:

- Country code (string): a 2-character notation of the country code
- VAT registration number (string): contains the complete registration number without the country code
- Date of request (DateTime): the date when the request was made
- Valid (boolean): flag indicating the registration number was valid (TRUE) or not (FALSE)
- Name (string): registered company name (if provided by EC member state)
- Address (string): registered company address (if provided by EC member state)

Stated on the European Commission website:
> To make an intra-Community supply without charging VAT, you **should ensure** that the person to whom you are supplying the goods is a taxable person in another Member State, and that the goods in question have left, or will leave your Member State to another MS. VAT-number should also be in the invoice.

More information at http://ec.europa.eu/taxation_customs/vies/faqvies.do#item16

## GDPR and privacy regulation of VAT within the EU

On May 25, 2018 the General Data Protection Regulation or GDPR becomes law within all 28 European Member States. Is this VIES service package going to be compliant with GDPR?

In short: yes.

The longer answer is that this VIES package only interacts with the service for VAT ID verification provided by the European Commission. VAT validation is mandatory in European countries and therefor this service is allowed as lawfulness and legal basis. Please read more about this in [European DPO-3816.1](http://ec.europa.eu/dpo-register/details.htm?id=40647). This service does not store any data itself or collects more information than what's strictly required by law and provided by the EC VIES service.

When you have implemented this service package in your own project, be sure that you're making sure you're just store the VAT ID, the timestamp of validation, the result of validation and optionally the given validation ID provided by the EC VIES service.

## Requirements

- Minimum PHP version: 7.4
- Guzzle: ^7.9
- Extension: json

Please read the [release notes](https://github.com/webatvantage/vies/releases) for details.

## Installation

This project is on [Packagist](https://packagist.org/packages/dragonbe/vies)!

To install the latest stable version use `composer require webatvantage/vies`.

To install specifically a version (e.g. 1.0.0), just add it to the command above, for example `composer require webatvantage/vies:1.0.0`

## Usage

Here's a usage example you can immediately execute on the command line (or in cron, worker or whatever) as this will most likely be your most common usecase.

### 1. Setting it up

```php
<?php

use Webatvantage\Vies\Vies;
use Webatvantage\Vies\Api\VatEuropeApi;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$viesService = new Vies(new VatEuropeApi());
```


### 2. Validate VAT

Now that we know the service is alive, we can start validating VAT ID's

#### 2.1. Simple usage

```php
$vatResult = $vat = $viesService->validateVat(
    'BE', // Country code
    '0203430576' // VAT ID
);

$vatResult->valid ? 'Valid' : 'Not valid'
```

#### 2.2. Validate format

```php
try
{
    $valid = $vat = $viesService->validateFormat(
        'BE', // Country code
        '0203430576' // VAT ID
    );
}
catch (ViesException $exception)
{
    $reason = $exception->getMessage()
}
```

#### 2.3. Handle errors

```php
function validateVat(string $countryCode, string $vatNumber, ?string &$error = null): bool
{
    try
    {
        $vat = $viesService->validateVat($countryCode, $vatNumber);
    }
    catch (InvalidCountryCodeException $exception)
    {
        $error = $exception->getMessage();

        return false;
    }
    catch (InvalidVatNumberFormatException)
    {
        $error = $exception->getMessage();

        return false;
    }

    if ($vat->valid === false)
    {
        $error = 'The provided VAT number is not valid';

        return false;
    }

    return true;
}
```

### 3. Formatting VAT

#### 3.1. Normalize VAT

```php

use Webatvantage\Vies\Vies;

$vatNumber = 'BE0203.430.576';

$vatNumber = Vies::normalizeVat($vatnumber); // BE0203430576

```

#### 3.2. Split VAT in country code and VAT ID

```php

use Webatvantage\Vies\Vies;

$vatNumber = 'BE0203430576';

['country' => $countryCode, 'id' => $vatNumber] = Vies::splitVatId($vatnumber); // ['country' => 'BE', 'id' => '203430576'];

```

### 4. Helper functions

#### 4.1. List European countries

```php

use Webatvantage\Vies\Vies;

$countries = Vies::listEuropeanCountries();

```

## Referenced on the web

- [Microsoft Dynamics GP - Dynamics GP real time EU tax registration number validation using VIES](http://timwappat.info/post/2013/08/22/Dynamics-GP-real-time-EU-tax-registration-number-validation-using-VIES)
- [Popular RIA law eu projects](https://libraries.io/search?keywords=RIA%2Claw%2Ceu)
- [PHP Code Examples - HotExamples.com](https://hotexamples.com/examples/dragonbe.vies/Vies/validateVat/php-vies-validatevat-method-examples.html)

## Clarification on exceptions

For Greece the [international country ISO code](https://www.iso.org/obp/ui/#iso:code:3166:GR) is **GR**, but for VAT IDN's they use the prefix **EL**. Thanks to [Johan Wilfer](https://github.com/johanwilfer) for [reporting this](https://github.com/DragonBe/vies/issues/57).

Since January 1, 2021 the UK is no longer a member of the European Union and as a result, the VIES service provided by the European Commission no longer validates VAT ID's for the UK. There is one exception though and that is for Northern Ireland (XI) for which VAT ID's can be validated using this library and the EC VIES service.

## Licence

DragonBe\Vies is released under the MIT Licence. See the bundled [LICENCE](LICENCE) file for details.
