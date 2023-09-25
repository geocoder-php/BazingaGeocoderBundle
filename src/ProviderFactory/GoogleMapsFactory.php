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
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GoogleMapsFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GoogleMaps::class, 'packageName' => 'geocoder-php/google-maps-provider'],
    ];

    /**
     * @param array{api_key: ?string, region: ?string, http_client: ?ClientInterface, httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['httplug_client'] ?? $this->httpClient ?? HttpClientDiscovery::find();

        return new GoogleMaps($httpClient, $config['region'], $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'http_client' => null,
            'region' => null,
            'api_key' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('region', ['string', 'null']);
        $resolver->setAllowedTypes('api_key', ['string', 'null']);

        $resolver->setDeprecated('httplug_client', 'willdurand/geocoder-bundle', '5.19', 'The option "%name%" is deprecated, use "http_client" instead.');
    }
}
