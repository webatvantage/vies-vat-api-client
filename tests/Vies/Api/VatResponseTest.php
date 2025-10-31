<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests\Api;

use PHPUnit\Framework\TestCase;
use Webatvantage\Vies\Api\VatResponse;

/**
 * Class CheckVatResponseTest
 */
class VatResponseTest extends TestCase
{
	public function validationProvider(): array
	{
		return  [
			[true],
			[false],
		];
	}

	/**
	 * Test that a VAT response can be created
	 *
	 * @dataProvider validationProvider
	 */
	public function testCanCreateResponseAtConstruct($validCheck)
	{
		$data = [
			'countryCode' => 'BE',
			'vatNumber' => '0474306343',
			'name' => 'BV WEB@VANTAGE',
			'address' => 'Slaapstraat 31/A 9890 Gavere',
		];

		$response = new VatResponse($data['countryCode'], $data['vatNumber'], $data['name'], $data['address'], new \DateTimeImmutable(), $validCheck);

		$this->assertSame($data['countryCode'], $response->countryCode);
		$this->assertSame($data['vatNumber'], $response->vatNumber);
		$this->assertSame($data['name'], $response->name);
		$this->assertSame($data['address'], $response->address);
		$this->assertSame($validCheck, $response->valid);
		$this->assertSame(date('Y-m-dP'), $response->requestedDate->format('Y-m-dP'));
	}

	/**
	 * @dataProvider validationProvider
	 */
	public function testCanCreateResponseWithoutNameAndAddressAtConstruct($validCheck)
	{
		$data = [
			'countryCode' => 'BE',
			'vatNumber' => '0474306343',
			'name' => '---',
			'address' => '---',
		];

		$response = new VatResponse($data['countryCode'], $data['vatNumber'], $data['name'], $data['address'], new \DateTimeImmutable(), $validCheck);

		$this->assertSame($data['countryCode'], $response->countryCode);
		$this->assertSame($data['vatNumber'], $response->vatNumber);
		$this->assertNull($response->name);
		$this->assertNull($response->address);
		$this->assertSame($validCheck, $response->valid);
		$this->assertSame(date('Y-m-dP'), $response->requestedDate->format('Y-m-dP'));
	}
}
