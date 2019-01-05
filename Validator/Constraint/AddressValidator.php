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

use Geocoder\Exception\Exception;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @author Tomas Norkūnas <norkunas.tom@gmail.com>
 */
class AddressValidator extends ConstraintValidator
{
    protected $addressGeocoder;

    public function __construct(Provider $addressGeocoder)
    {
        $this->addressGeocoder = $addressGeocoder;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Address) {
            throw new UnexpectedTypeException($constraint, Address::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            if (class_exists(UnexpectedValueException::class)) {
                throw new UnexpectedValueException($value, 'string');
            } else {
                throw new UnexpectedTypeException($value, 'string');
            }
        }

        $value = (string) $value;

        try {
            $collection = $this->addressGeocoder->geocodeQuery(GeocodeQuery::create($value));

            if ($collection->isEmpty()) {
                $this->buildViolation($constraint, $value);
            }
        } catch (Exception $e) {
            $this->buildViolation($constraint, $value);
        }
    }

    private function buildViolation(Address $constraint, string $address)
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ address }}', $this->formatValue($address))
            ->setInvalidValue($address)
            ->setCode(Address::INVALID_ADDRESS_ERROR)
            ->addViolation();
    }
}
