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
use Geocoder\Provider\OpenCage\OpenCage;
use Geocoder\Provider\PickPoint\PickPoint;
use Geocoder\Provider\TomTom\TomTom;
use Geocoder\Provider\Yandex\Yandex;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Loader\YamlFileLoader;

final class YandexFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Yandex::class, 'packageName' => 'geocoder-php/yandex-provider'],
    ];

    protected function getProvider(array $config)
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new Yandex($httplug, $config['toponym']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'toponym' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('toponym', ['string', 'null']);
    }
}
