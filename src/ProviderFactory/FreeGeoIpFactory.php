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
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FreeGeoIpFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => FreeGeoIp::class, 'packageName' => 'geocoder-php/free-geoip-provider'],
    ];

    /**
     * @param array{base_url: string, http_client: ?ClientInterface, httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $config['httplug_client'] ?? $this->httpClient ?? HttpClientDiscovery::find();

        return new FreeGeoIp($httpClient, $config['base_url']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'http_client' => null,
            'base_url' => 'https://freegeoip.app/json/%s',
        ]);

        $resolver->setAllowedTypes('httplug_client', ['object', 'null']);
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('base_url', ['string']);

        $resolver->setDeprecated('httplug_client', 'willdurand/geocoder-bundle', '5.19', 'The option "httplug_client" is deprecated, use "http_client" instead.');
    }
}
