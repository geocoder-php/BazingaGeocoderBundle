<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\Mapping\Driver;

interface DriverInterface
{
    public function isGeocodeable($object);

    public function loadMetadataFromObject($object);
}
