<?php

namespace Webatvantage\Vies;

use DateTimeInterface;

class ExcludedCountry
{
	protected string $code;

	protected string $name;

	protected DateTimeInterface $excluded;

	protected string $reason;

	/**
	 * @param string $code
	 * @param string $name
	 * @param DateTimeInterface $excluded
	 * @param string $reason
	 */
	public function __construct(string $code, string $name, DateTimeInterface $excluded, string $reason)
	{
		$this->code = $code;
		$this->name = $name;
		$this->excluded = $excluded;
		$this->reason = $reason;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getExcluded(): DateTimeInterface
	{
		return $this->excluded;
	}

	public function getReason(): string
	{
		return $this->reason;
	}
}
