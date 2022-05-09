<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\Mapbox\Mapbox;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MapboxFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Mapbox::class, 'packageName' => 'geocoder-php/mapbox-provider'],
    ];

    /**
     * @phpstan-param array{api_key: string, country: ?string, mode: string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new Mapbox($httplug, $config['api_key'], $config['country'], $config['mode']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'country' => null,
            'mode' => Mapbox::GEOCODING_MODE_PLACES,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('api_key', ['string']);
        $resolver->setAllowedTypes('mode', ['string']);
        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('country', ['string', 'null']);
    }
}
