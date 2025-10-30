<?php

namespace Webatvantage\Vies;

interface ValidatorInterface
{
	public function validate(string $vatNumber): bool;
}
