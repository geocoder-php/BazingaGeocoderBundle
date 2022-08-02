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
    /**
     * @param object $object
     */
    public function isGeocodeable($object): bool;

    /**
     * @param object $object
     *
     * @return ClassMetadata
     */
    public function loadMetadataFromObject($object);
}
