<?php

namespace Webatvantage\Vies\Api;

use DateTimeInterface;

class VatResponse implements \JsonSerializable, \Stringable
{
	public string $countryCode;

	public string $vatNumber;

	public DateTimeInterface $requestedDate;

	public bool $valid;

	public ?string $name;

	public ?string $address;

	public function __construct(string $countryCode, string $vatNumber, string $name, string $address, DateTimeInterface $requestedDate, bool $valid)
	{
		$this->countryCode = $countryCode;
		$this->vatNumber = $vatNumber;
		$this->name = $name === '---' ? null : $name; // VIES returns '---' when no name is available
		$this->address = $address === '---' ? null : $address; // VIES returns '---' when no address is available
		$this->requestedDate = $requestedDate;
		$this->valid = $valid;
	}

	public function toArray(): array
	{
		return [
			'countryCode' => $this->countryCode,
			'vatNumber' => $this->vatNumber,
			'name' => $this->name,
			'address' => $this->address,
			'requestedDate' => $this->requestedDate->format(DATE_ATOM),
			'valid' => $this->valid,
		];
	}

	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	public function __toString(): string
	{
		return "{$this->countryCode}{$this->vatNumber}";
	}
}
