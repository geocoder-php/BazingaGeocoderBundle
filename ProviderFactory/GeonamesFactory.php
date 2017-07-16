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
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeonamesFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => Geonames::class, 'packageName' => 'geocoder-php/geonames-provider'],
    ];

    protected function getProvider(array $config)
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new Geonames($httplug, $config['username']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setRequired('username');
        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('username', ['string']);
    }
}
