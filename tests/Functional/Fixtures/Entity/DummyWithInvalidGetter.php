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
class DummyWithInvalidGetter
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: Types::FLOAT)]
    #[Latitude]
    private ?float $latitude = null;

    #[Column(type: Types::FLOAT)]
    #[Longitude]
    private ?float $longitude = null;

    #[Column(type: Types::STRING)]
    private ?string $_address = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setAddress(string $address): void
    {
        $this->_address = $address;
    }

    #[Address]
    public function getAddress(mixed $requiredParameter): ?string
    {
        return $this->_address;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }
}
