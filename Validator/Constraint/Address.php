<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Tomas Norkūnas <norkunas.tom@gmail.com>
 */
class Address extends Constraint
{
    const INVALID_ADDRESS_ERROR = '2243aa07-2ea7-4eb7-962c-6a9586593f2c';

    protected static $errorNames = [
        self::INVALID_ADDRESS_ERROR => 'INVALID_ADDRESS_ERROR',
    ];

    public $service = AddressValidator::class;

    public $message = 'Address {{ address }} is not valid.';

    public function validatedBy()
    {
        return $this->service;
    }
}
