<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Mapping\Driver;

use Bazinga\GeocoderBundle\Mapping\ClassMetadata;

interface DriverInterface
{
    public function isGeocodeable(object $object): bool;

    public function loadMetadataFromObject(object $object): ClassMetadata;
}
