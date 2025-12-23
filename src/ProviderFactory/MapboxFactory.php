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
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MapboxFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Mapbox::class, 'packageName' => 'geocoder-php/mapbox-provider'],
    ];

    /**
     * @param array{api_key: string, country: ?string, mode: string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new Mapbox($httpClient, $config['api_key'], $config['country'], $config['mode']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'http_client' => null,
            'country' => null,
            'mode' => Mapbox::GEOCODING_MODE_PLACES,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('api_key', ['string']);
        $resolver->setAllowedTypes('mode', ['string']);
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('country', ['string', 'null']);
    }
}
