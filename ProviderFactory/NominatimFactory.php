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
use Geocoder\Provider\GeoIPs\GeoIPs;
use Geocoder\Provider\Geonames\Geonames;
use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\HostIp\HostIp;
use Geocoder\Provider\IpInfoDb\IpInfoDb;
use Geocoder\Provider\Mapzen\Mapzen;
use Geocoder\Provider\Nominatim\Nominatim;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NominatimFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Nominatim::class, 'packageName' => 'geocoder-php/nominatim-provider'],
    ];

    protected function getProvider(array $config)
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new Nominatim($httplug, $config['root_url']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'root_url' => 'https://nominatim.openstreetmap.org',
        ]);

        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('root_url', ['string']);

    }
}
