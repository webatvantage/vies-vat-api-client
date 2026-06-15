<?php

namespace Webatvantage\Vies\Api;

use DateTimeInterface;

class VatResponse implements \JsonSerializable
{
	public string $countryCode;

	public string $vatNumber;

	public DateTimeInterface $requestedDate;

	public bool $valid;

	public ?string $name;

	public ?string $address;

	/** The validation identifier returned by VIES (used as proof of consultation). */
	public ?string $requestIdentifier;

	public function __construct(string $countryCode, string $vatNumber, ?string $name, ?string $address, DateTimeInterface $requestedDate, bool $valid, ?string $requestIdentifier = null)
	{
		$this->countryCode = $countryCode;
		$this->vatNumber = $vatNumber;
		$this->name = self::nullify($name);
		$this->address = self::nullify($address);
		$this->requestedDate = $requestedDate;
		$this->valid = $valid;
		$this->requestIdentifier = self::nullify($requestIdentifier);
	}

	/**
	 * Treats the VIES "no value" sentinel ('---'), empty strings and missing values as null.
	 */
	private static function nullify(?string $value): ?string
	{
		return ($value === null || $value === '' || $value === '---') ? null : $value;
	}

	/**
	 * @return array<string, string|bool|null>
	 */
	public function toArray(): array
	{
		return [
			'countryCode' => $this->countryCode,
			'vatNumber' => $this->vatNumber,
			'requestIdentifier' => $this->requestIdentifier,
			'name' => $this->name,
			'address' => $this->address,
			'requestedDate' => $this->requestedDate->format(DATE_ATOM),
			'valid' => $this->valid,
		];
	}

	/**
	 * @return array<string, string|bool|null>
	 */
	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	public function __toString(): string
	{
		return "{$this->countryCode}{$this->vatNumber}";
	}
}
