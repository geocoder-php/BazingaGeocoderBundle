<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Functional\Fixtures\Entity;

use Bazinga\GeocoderBundle\Mapping\Attributes\Address;
use Bazinga\GeocoderBundle\Mapping\Attributes\Country;
use Bazinga\GeocoderBundle\Mapping\Attributes\East;
use Bazinga\GeocoderBundle\Mapping\Attributes\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Attributes\Latitude;
use Bazinga\GeocoderBundle\Mapping\Attributes\Locality;
use Bazinga\GeocoderBundle\Mapping\Attributes\Longitude;
use Bazinga\GeocoderBundle\Mapping\Attributes\North;
use Bazinga\GeocoderBundle\Mapping\Attributes\PostalCode;
use Bazinga\GeocoderBundle\Mapping\Attributes\South;
use Bazinga\GeocoderBundle\Mapping\Attributes\StreetName;
use Bazinga\GeocoderBundle\Mapping\Attributes\StreetNumber;
use Bazinga\GeocoderBundle\Mapping\Attributes\SubLocality;
use Bazinga\GeocoderBundle\Mapping\Attributes\West;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
#[Geocodeable(provider: 'acme')]
class DummyWithProperty
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    public ?int $id = null;

    #[Column(type: Types::FLOAT)]
    #[Latitude]
    public ?float $latitude = null;

    #[Column(type: Types::FLOAT)]
    #[Longitude]
    public ?float $longitude = null;

    #[Column(type: Types::STRING)]
    #[Address]
    public ?string $address = null;

    #[Column(type: Types::FLOAT, nullable: true)]
    #[North]
    public ?float $north = null;

    #[Column(type: Types::FLOAT, nullable: true)]
    #[South]
    public ?float $south = null;

    #[Column(type: Types::FLOAT, nullable: true)]
    #[East]
    public ?float $east = null;

    #[Column(type: Types::FLOAT, nullable: true)]
    #[West]
    public ?float $west = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[StreetNumber]
    public int|string|null $streetNumber = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[StreetName]
    public ?string $streetName = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[Locality]
    public ?string $locality = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[PostalCode]
    public ?string $postalCode = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[SubLocality]
    public ?string $subLocality = null;

    #[Column(type: Types::STRING, nullable: true)]
    #[Country]
    public ?string $country = null;
}
