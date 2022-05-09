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

use Geocoder\Provider\AlgoliaPlaces\AlgoliaPlaces;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AlgoliaFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => AlgoliaPlaces::class, 'packageName' => 'geocoder-php/algolia-places-provider'],
    ];

    /**
     * @phpstan-param array{api_key: ?string, app_id: ?string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new AlgoliaPlaces($httplug, $config['api_key'], $config['app_id']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'api_key' => null,
            'app_id' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('api_key', ['string', 'null']);
        $resolver->setAllowedTypes('app_id', ['string', 'null']);
    }
}
