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
use Bazinga\GeocoderBundle\Mapping\Attributes\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Attributes\Latitude;
use Bazinga\GeocoderBundle\Mapping\Attributes\Longitude;
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
}
