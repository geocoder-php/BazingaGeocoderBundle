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

use Geocoder\Provider\FreeGeoIp\FreeGeoIp;
use Geocoder\Provider\Provider;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FreeGeoIpFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => FreeGeoIp::class, 'packageName' => 'geocoder-php/free-geoip-provider'],
    ];

    /**
     * @phpstan-param array{base_url: string, httplug_client: ?HttpClient} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new FreeGeoIp($httplug, $config['base_url']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'base_url' => 'https://freegeoip.app/json/%s',
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('base_url', ['string']);
    }
}
