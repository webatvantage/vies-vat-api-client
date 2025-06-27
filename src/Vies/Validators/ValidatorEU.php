<?php

declare (strict_types=1);

/**
 * \DragonBe\Vies
 *
 * @author  Paweł Krzaczkowski <krzaczek+github@gmail.com>
 * @license  MIT
 */

namespace DragonBe\Vies\Validators;

use DragonBe\Vies\VatValidator;

/**
 * Class ValidatorEU
 *
 * @package DragonBe\Vies\Validator
*/
class ValidatorEU extends VatValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(string $vatNumber): bool
    {
        return false;
    }
}
