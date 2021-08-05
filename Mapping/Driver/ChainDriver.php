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

use Bazinga\GeocoderBundle\Mapping\Exception\MappingException;

/**
 * @author Pierre du Plessis <pdples@gmail.com>
 */
class ChainDriver implements DriverInterface
{
    private $drivers;

    public function __construct(iterable $drivers)
    {
        $this->drivers = $drivers;
    }

    public function isGeocodeable($object): bool
    {
        foreach ($this->drivers as $driver) {
            if ($driver->isGeocodeable($object)) {
                return true;
            }
        }

        return false;
    }

    public function loadMetadataFromObject($object)
    {
        foreach ($this->drivers as $driver) {
            try {
                return $driver->loadMetadataFromObject($object);
            } catch (MappingException $exception) {
                continue;
            }
        }

        throw new MappingException(sprintf('The class %s is not geocodeable', get_class($object)));
    }
}
