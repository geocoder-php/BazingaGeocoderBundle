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

use Geocoder\Provider\IpInfoDb\IpInfoDb;
use Geocoder\Provider\Provider;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IpInfoDbFactory extends AbstractFactory
{
    protected static array $dependencies = [
        ['requiredClass' => IpInfoDb::class, 'packageName' => 'geocoder-php/ip-info-db-provider'],
    ];

    /**
     * @param array{api_key: string, precision: string, http_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httpClient = $config['http_client'] ?? $this->httpClient ?? Psr18ClientDiscovery::find();

        return new IpInfoDb($httpClient, $config['api_key'], $config['precision']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'http_client' => null,
            'precision' => 'city',
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('http_client', ['object', 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
        $resolver->setAllowedTypes('precision', ['string']);
    }
}
