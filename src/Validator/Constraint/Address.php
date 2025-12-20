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
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Address extends Constraint
{
    public const INVALID_ADDRESS_ERROR = '2243aa07-2ea7-4eb7-962c-6a9586593f2c';

    protected const ERROR_NAMES = [
        self::INVALID_ADDRESS_ERROR => 'INVALID_ADDRESS_ERROR',
    ];

    /**
     * @var string
     */
    public $service = AddressValidator::class;

    /**
     * @var string
     */
    public $message = 'Address {{ address }} is not valid.';

    /**
     * @param string[]|null $options
     */
    public function __construct(?array $options = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return $this->service;
    }
}
