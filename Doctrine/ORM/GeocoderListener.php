<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Doctrine\ORM;

use Bazinga\GeocoderBundle\Mapping\Driver\DriverInterface;
use Geocoder\Provider\Provider;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GeocoderListener extends GeocodeEntityListener
{
    public function __construct(Provider $geocoder, DriverInterface $driver)
    {
        @trigger_error(sprintf('The class "%s" is deprecated and will be removed from a future version. Please remove it from your service definition.', self::class));

        $locator = new ServiceLocator([
            'bazinga_geocoder.provider.' => function () use ($geocoder) {
                return $geocoder;
            },
        ]);

        parent::__construct($locator, $driver);
    }
}
