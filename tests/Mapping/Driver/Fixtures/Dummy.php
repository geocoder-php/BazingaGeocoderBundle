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

use Bazinga\GeocoderBundle\Mapping\Annotations\Address;
use Bazinga\GeocoderBundle\Mapping\Annotations\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Annotations\Latitude;
use Bazinga\GeocoderBundle\Mapping\Annotations\Longitude;

/**
 * @Geocodeable
 */
#[Geocodeable]
final class Dummy
{
    /**
     * @Latitude
     */
    #[Latitude]
    public $latitude;

    /**
     * @Longitude
     */
    #[Longitude]
    public $longitude;

    /**
     * @Address
     */
    #[Address]
    public $address;
}
