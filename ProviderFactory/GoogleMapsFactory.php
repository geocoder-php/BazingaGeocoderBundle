<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\GoogleMaps\GoogleMaps;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GoogleMapsFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GoogleMaps::class, 'packageName' => 'geocoder-php/google-maps-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new GoogleMaps($httplug, $config['region'], $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'region' => null,
            'api_key' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('region', ['string', 'null']);
        $resolver->setAllowedTypes('api_key', ['string', 'null']);
    }
}
