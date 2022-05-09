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

use Geocoder\Provider\Geonames\Geonames;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeonamesFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => Geonames::class, 'packageName' => 'geocoder-php/geonames-provider'],
    ];

    /**
     * @phpstan-param array{username: string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new Geonames($httplug, $config['username']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setRequired('username');
        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('username', ['string']);
    }
}
