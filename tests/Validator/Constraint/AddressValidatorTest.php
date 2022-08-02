<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Validator\Constraint;

use Bazinga\GeocoderBundle\Validator\Constraint\Address;
use Bazinga\GeocoderBundle\Validator\Constraint\AddressValidator;
use Geocoder\Provider\Nominatim\Nominatim;
use Http\Client\Curl\Client;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class AddressValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): AddressValidator
    {
        $geocoder = Nominatim::withOpenStreetMapServer(new Client(), 'BazingaGeocoderBundle/Test');

        return new AddressValidator($geocoder);
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Address());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Address());

        $this->assertNoViolation();
    }

    public function testExpectsStringCompatibleType(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new Address());
    }

    public function testValidAddress(): void
    {
        $this->validator->validate('Berlin, Germany', new Address());

        $this->assertNoViolation();
    }

    public function testInvalidAddress(): void
    {
        $address = 'Bifrost, Nine Realms';

        $constraint = new Address([
            'message' => 'myMessage {{ address }}',
        ]);

        $this->validator->validate($address, $constraint);

        $this->buildViolation('myMessage {{ address }}')
            ->setParameter('{{ address }}', '"'.$address.'"')
            ->setInvalidValue($address)
            ->setCode(Address::INVALID_ADDRESS_ERROR)
            ->assertRaised();
    }
}
