<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\BingMaps\BingMaps;
use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Provider\Geoip\Geoip;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeoipFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Geoip::class, 'packageName' => 'geocoder-php/geoip-provider'],
    ];

    protected function getProvider(array $config)
    {
        return new Geoip();
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
    }
}
