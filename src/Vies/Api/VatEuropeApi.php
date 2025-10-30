<?php

namespace Webatvantage\Vies\Api;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Webatvantage\Vies\Exceptions\ViesServiceException;

class VatEuropeApi
{
	public const API_URL = 'https://ec.europa.eu/taxation_customs/vies/rest-api/check-vat-number';

	private string $url;

	private ?ClientInterface $client;

	public function __construct(string $url = self::API_URL, ?ClientInterface $client = null)
	{
		$this->url = $url;
		$this->client = $client ?? new Client([
			'headers' => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			],
		]);
	}

	/**
	 * @throws ViesServiceException
	 * @throws \JsonException
	 * @throws \Exception
	 */
	public function retrieveVatInstance(string $countryCode, string $vatNumber): VatResponse
	{
		try
		{
			$response = $this->client->post($this->url, [
				'json' => [
					'countryCode' => $countryCode,
					'vatNumber' => $vatNumber,
				],
			]);
		}
		catch (ClientExceptionInterface $exception)
		{
			$message = sprintf(
				'Back-end VIES service cannot validate the VAT number "%s%s" at this moment. '
				. 'The service responded with the critical error "%s". This is probably a temporary '
				. 'problem. Please try again later.',
				$countryCode,
				$vatNumber,
				$exception->getMessage(),
			);

			throw new ViesServiceException($message, 0, $exception);
		}

		$contents = (string) $response->getBody();

		$data = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);

		return new VatResponse(
			$data->countryCode,
			$data->vatNumber,
			$data->name,
			$data->address,
			new \DateTime($data->requestDate),
			$data->valid,
		);
	}
}
