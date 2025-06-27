<?php

namespace DragonBe\Vies;

use DateTimeInterface;

class VatInstance implements \JsonSerializable, \Stringable
{
    public string $countryCode;

    public string $vatNumber;

    public DateTimeInterface $date;

    public bool $valid;

    public ?string $name;

    public ?string $address;

    public function __construct(string $countryCode, string $vatNumber, DateTimeInterface $date, bool $valid, ?string $name = null, ?string $address = null)
    {
        $this->countryCode = $countryCode;
        $this->vatNumber = $vatNumber;
        $this->date = $date;
        $this->valid = $valid;
        $this->name = $name;
        $this->address = $address;
    }

    public function toArray(): array
    {
        return [
            'countryCode' => $this->countryCode,
            'vatNumber' => $this->vatNumber,
            'date' => $this->date->format('Y-m-d'),
            'valid' => $this->valid,
            'name' => $this->name,
            'address' => $this->address,
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