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
class DummyWithEmptyProperty
{
    /**
     * @Id @GeneratedValue
     *
     * @Column(type="integer")
     */
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    public $id;

    /**
     * @Latitude
     *
     * @Column(nullable=true)
     */
    #[Column(nullable: true)]
    #[Latitude]
    public $latitude;

    /**
     * @Longitude
     *
     * @Column(nullable=true)
     */
    #[Column(nullable: true)]
    #[Longitude]
    public $longitude;

    /**
     * @Address
     *
     * @Column
     */
    #[Column]
    #[Address]
    public $address;
}
