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

use Geocoder\Provider\GeoIPs\GeoIPs;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated since 5.6, to be removed in 6.0. See https://github.com/geocoder-php/Geocoder/issues/965
 */
final class GeoIPsFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GeoIPs::class, 'packageName' => 'geocoder-php/geoips-provider'],
    ];

    /**
     * @param array{api_key: string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        @trigger_error('Bazinga\GeocoderBundle\ProviderFactory\GeoIPsFactory is deprecated since 5.6, to be removed in 6.0. See https://github.com/geocoder-php/Geocoder/issues/965', E_USER_DEPRECATED);

        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();
        assert($httplug instanceof HttpClient);

        return new GeoIPs($httplug, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
    }
}
