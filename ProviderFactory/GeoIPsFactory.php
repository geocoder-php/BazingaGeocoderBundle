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

    protected function getProvider(array $config): Provider
    {
        @trigger_error('Bazinga\GeocoderBundle\ProviderFactory\GeoIPsFactory is deprecated since 5.6, to be removed in 6.0. See https://github.com/geocoder-php/Geocoder/issues/965', E_USER_DEPRECATED);

        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new GeoIPs($httplug, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
    }
}
