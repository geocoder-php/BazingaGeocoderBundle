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
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IpInfoDbFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => IpInfoDb::class, 'packageName' => 'geocoder-php/ip-info-db-provider'],
    ];

    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new IpInfoDb($httplug, $config['api_key'], $config['precision']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
            'precision' => 'city',
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
        $resolver->setAllowedTypes('precision', ['string']);
    }
}
