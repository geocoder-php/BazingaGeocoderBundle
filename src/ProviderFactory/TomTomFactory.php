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

use Geocoder\Provider\Provider;
use Geocoder\Provider\TomTom\TomTom;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TomTomFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => TomTom::class, 'packageName' => 'geocoder-php/tomtom-provider'],
    ];

    /**
     * @param array{api_key: string, httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new TomTom($httplug, $config['api_key']);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setRequired('api_key');
        $resolver->setAllowedTypes('httplug_client', [ClientInterface::class, 'null']);
        $resolver->setAllowedTypes('api_key', ['string']);
    }
}
