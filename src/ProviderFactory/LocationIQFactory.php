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
     * @param array{api_key: string, httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new LocationIQ($httplug, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', [ClientInterface::class, 'null']);
        $resolver->setRequired(['api_key']);
        $resolver->setAllowedTypes('api_key', ['string']);
    }
}
