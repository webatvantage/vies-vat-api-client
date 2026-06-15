<?php

declare (strict_types=1);

namespace Webatvantage\Vies\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Webatvantage\Vies\Api\VatEuropeApi;
use Webatvantage\Vies\Api\VatResponse;
use Webatvantage\Vies\Exceptions\CountryNoLongerSupportedException;
use Webatvantage\Vies\Exceptions\InvalidCountryCodeException;
use Webatvantage\Vies\Exceptions\InvalidVatNumberFormatException;
use Webatvantage\Vies\Exceptions\ViesServiceException;
use Webatvantage\Vies\Vies;

/**
 * @coversDefaultClass \Webatvantage\Vies\Vies
 */
class ViesTest extends TestCase
{
	public function vatNumberProvider(): array
	{
		return [
			['0123456749', '0123456749'],
			['0123 456 749', '0123456749'],
			['0123.456.749', '0123456749'],
			['0123-456-749', '0123456749'],
			['be0123456749', 'BE0123456749'],
			[' 0123456749 ', '0123456749'],
		];
	}

	/**
	 * @covers ::normalizeVat
	 *
	 * @dataProvider vatNumberProvider
	 */
	public function testVatNumberIsNormalized(string $vatNumber, string $normalized)
	{
		$this->assertSame($normalized, Vies::normalizeVat($vatNumber));
	}

	/**
	 * @covers ::splitVatId
	 */
	public function testSplitVatId()
	{
		['country' => $country, 'id' => $id] = Vies::splitVatId('BE0123456749');

		$this->assertSame('BE', $country);
		$this->assertSame('0123456749', $id);
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatReturnsValidResponse()
	{
		$vies = $this->viesWithResponses([
			$this->jsonResponse([
				'countryCode' => 'BE',
				'vatNumber' => '0417710407',
				'requestDate' => '2024-01-15T00:00:00.000+01:00',
				'valid' => true,
				'requestIdentifier' => 'WAPIAAAAXi8a',
				'name' => 'BV WEB@VANTAGE',
				'address' => 'Slaapstraat 31 9890 Gavere',
			]),
		]);

		$response = $vies->validateVat('BE', '0417710407');

		$this->assertInstanceOf(VatResponse::class, $response);
		$this->assertTrue($response->valid);
		$this->assertSame('BE', $response->countryCode);
		$this->assertSame('0417710407', $response->vatNumber);
		$this->assertSame('BV WEB@VANTAGE', $response->name);
		$this->assertSame('Slaapstraat 31 9890 Gavere', $response->address);
		$this->assertSame('WAPIAAAAXi8a', $response->requestIdentifier);
		$this->assertSame('2024-01-15', $response->requestedDate->format('Y-m-d'));
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatReturnsInvalidResponseWithoutTraderData()
	{
		$vies = $this->viesWithResponses([
			$this->jsonResponse([
				'countryCode' => 'BE',
				'vatNumber' => '0627515170',
				'requestDate' => '2024-01-15T00:00:00.000+01:00',
				'valid' => false,
				'requestIdentifier' => '',
				'name' => '---',
				'address' => '---',
			]),
		]);

		$response = $vies->validateVat('BE', '0627515170');

		$this->assertFalse($response->valid);
		$this->assertNull($response->name);
		$this->assertNull($response->address);
		$this->assertNull($response->requestIdentifier);
	}

	/**
	 * VIES returns trader information in the language/alphabet of the member
	 * state; make sure non-latin values survive the round-trip.
	 *
	 * @covers ::validateVat
	 */
	public function testValidateVatPreservesNonLatinTraderData()
	{
		$vies = $this->viesWithResponses([
			$this->jsonResponse([
				'countryCode' => 'EL',
				'vatNumber' => '999645865',
				'requestDate' => '2024-01-15T00:00:00.000+01:00',
				'valid' => true,
				'requestIdentifier' => 'WAPIAAAAXi9b',
				'name' => 'ΤΡΑΙΝΟΣΕ',
				'address' => 'ΚΑΡΟΛΟΥ 1-3, 10437 ΑΘΗΝΑ',
			]),
		]);

		$response = $vies->validateVat('EL', '999645865');

		$this->assertTrue($response->valid);
		$this->assertSame('ΤΡΑΙΝΟΣΕ', $response->name);
		$this->assertSame('ΚΑΡΟΛΟΥ 1-3, 10437 ΑΘΗΝΑ', $response->address);
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatThrowsForUnknownCountryCode()
	{
		$vies = $this->viesWithResponses([]);

		$this->expectException(InvalidCountryCodeException::class);

		$vies->validateVat('AA', '0123456749');
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatThrowsForInvalidFormat()
	{
		$vies = $this->viesWithResponses([]);

		$this->expectException(InvalidVatNumberFormatException::class);

		$vies->validateVat('BE', '123');
	}

	/**
	 * The United Kingdom is no longer served by VIES since Brexit, so it should
	 * fail fast with a dedicated exception before any network call is made.
	 *
	 * @covers ::validateVat
	 */
	public function testValidateVatThrowsForExcludedCountry()
	{
		$vies = $this->viesWithResponses([]);

		$this->expectException(CountryNoLongerSupportedException::class);
		$this->expectExceptionMessage(
			'Country United Kingdom is no longer supported by VIES services provided by EC since 2021-01-01 because of Brexit',
		);

		$vies->validateVat('GB', '434031494');
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatWrapsTransportErrorsInServiceException()
	{
		$vies = $this->viesWithResponses([
			new ConnectException('Could not resolve host', new Request('POST', VatEuropeApi::API_URL)),
		]);

		$this->expectException(ViesServiceException::class);

		$vies->validateVat('BE', '0417710407');
	}

	/**
	 * @covers ::validateVat
	 */
	public function testValidateVatWrapsHttpErrorsInServiceException()
	{
		$vies = $this->viesWithResponses([
			$this->jsonResponse(['actionSucceed' => false, 'errorWrappers' => []], 500),
		]);

		$this->expectException(ViesServiceException::class);

		$vies->validateVat('BE', '0417710407');
	}

	/**
	 * Build a Vies instance backed by a mocked HTTP client that returns the
	 * queued responses/exceptions in order.
	 *
	 * @param array<\Psr\Http\Message\ResponseInterface|\Throwable> $queue
	 */
	private function viesWithResponses(array $queue): Vies
	{
		$handlerStack = HandlerStack::create(new MockHandler($queue));
		$client = new Client(['handler' => $handlerStack]);

		return new Vies(new VatEuropeApi(VatEuropeApi::API_URL, $client));
	}

	/**
	 * @param array<string,mixed> $data
	 */
	private function jsonResponse(array $data, int $status = 200): Response
	{
		return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
	}
}
