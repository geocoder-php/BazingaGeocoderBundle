<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\LocationIQ\LocationIQ;
use Geocoder\Provider\Provider;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LocationIQFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => LocationIQ::class, 'packageName' => 'geocoder-php/locationiq-provider'],
    ];

    /**
     * @param array{api_key: string, http_client: ?ClientInterface, httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $config['httplug_client'] ?? $this->httpClient ?? HttpClientDiscovery::find();

        return new LocationIQ($httpClient, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'http_client' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setRequired(['api_key']);
        $resolver->setAllowedTypes('api_key', ['string']);

        $resolver->setDeprecated('httplug_client', 'willdurand/geocoder-bundle', '5.19', 'The option "httplug_client" is deprecated, use "http_client" instead.');
    }
}
