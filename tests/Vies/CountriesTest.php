<?php

namespace Vies;

use PHPUnit\Framework\TestCase;
use Webatvantage\Vies\Countries;

/**
 * @coversDefaultClass \Webatvantage\Vies\Countries
 */
class CountriesTest extends TestCase
{
	/**
	 * @covers ::
	 */
	public function testRetrievingListOfEuropeanCountriesStatically()
	{
		$this->assertCount(Countries::VIES_EU_COUNTRY_TOTAL, Countries::europeanCountries());
	}

	public function testItValidatesIsEuFunction()
	{
		$this->assertTrue(Countries::isEu('DE'));
		$this->assertFalse(Countries::isEu('US'));
		$this->assertFalse(Countries::isEu('EU'));
		$this->assertFalse(Countries::isEu('GB'));
		$this->assertTrue(Countries::isEu('BE'));
		$this->assertFalse(Countries::isEu('UK'));
		$this->assertFalse(Countries::isEu('EU'));
	}
}
