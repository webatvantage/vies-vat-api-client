<?php

namespace Webatvantage\Vies;

class Country
{
	private string $code;

	private string $name;

	/** @var class-string<VatValidator> */
	private string $validatorClass;

	/** @var array<string> */
	private array $formats;

	/**
	 * @param string $code
	 * @param string $name
	 * @param class-string<VatValidator> $validatorClass
	 * @param array<string> $formats
	 */
	public function __construct(string $code, string $name, string $validatorClass, array $formats)
	{
		$this->code = $code;
		$this->name = $name;
		$this->validatorClass = $validatorClass;
		$this->formats = $formats;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getValidatorClass(): string
	{
		return $this->validatorClass;
	}

	public function getValidator(): VatValidator
	{
		$className = $this->getValidatorClass();

		return new $className();
	}

	/**
	 * @return array<string>
	 */
	public function getFormats(): array
	{
		return $this->formats;
	}

	/**
	 * @return array<string>
	 */
	public function getReadableFormats(): array
	{
		return array_map(fn (string $format) => $this->toReadableFormat($format), $this->getFormats());
	}

	protected function toReadableFormat(string $format): string
	{
		$numbers = range(1, 9);
		$letters = range('A', 'Z');

		$characters = array_map(
			function (string $character) use (&$numbers, &$letters) {
				switch ($character)
				{
					case '9':
						return array_shift($numbers);
					case 'L':
						return array_shift($letters);
					case 'X':
						return rand(0, 1) ? array_shift($numbers) : array_shift($letters);
					default:
						return $character;
				}
			},
			str_split($format),
		);

		return $this->code . implode('', $characters);
	}
}
