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
}
