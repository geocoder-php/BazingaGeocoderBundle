<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\Mapping\Driver\Fixtures;

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

#[Geocodeable(provider: 'acme')]
final class Dummy
{
    #[Latitude]
    public ?float $latitude;

    #[Longitude]
    public ?float $longitude;

    #[Address]
    public ?string $address;

    #[North]
    public ?float $north;

    #[South]
    public ?float $south;

    #[East]
    public ?float $east;

    #[West]
    public ?float $west;

    #[StreetNumber]
    public int|string|null $streetNumber;

    #[StreetName]
    public ?string $streetName;

    #[Locality]
    public ?string $locality;

    #[PostalCode]
    public ?string $postalCode;

    #[SubLocality]
    public ?string $subLocality;

    #[Country]
    public ?string $country;
}
