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
use Bazinga\GeocoderBundle\Mapping\Attributes\Geocodeable;
use Bazinga\GeocoderBundle\Mapping\Attributes\Latitude;
use Bazinga\GeocoderBundle\Mapping\Attributes\Longitude;

#[Geocodeable(provider: 'acme')]
final class Dummy
{
    #[Latitude]
    public $latitude;

    #[Longitude]
    public $longitude;

    #[Address]
    public $address;
}
