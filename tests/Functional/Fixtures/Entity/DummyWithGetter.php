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

use Bazinga\GeocoderBundle\Mapping\Annotations\Address;
use Bazinga\GeocoderBundle\Mapping\Annotations\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Annotations\Latitude;
use Bazinga\GeocoderBundle\Mapping\Annotations\Longitude;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

/**
 * @Geocodeable
 *
 * @Entity
 */
#[Entity]
#[Geocodeable]
class DummyWithGetter
{
    /**
     * @Id @GeneratedValue
     *
     * @Column(type="integer")
     */
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private $id;

    /**
     * @Latitude
     *
     * @Column
     */
    #[Column]
    #[Latitude]
    private $latitude;

    /**
     * @Longitude
     *
     * @Column
     */
    #[Column]
    #[Longitude]
    private $longitude;

    /**
     * @Column
     */
    #[Column]
    private $_address;

    public function setAddress($address)
    {
        $this->_address = $address;
    }

    /**
     * @Address
     */
    #[Address]
    public function getAddress()
    {
        return $this->_address;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}
