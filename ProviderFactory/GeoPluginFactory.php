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

use Geocoder\Provider\GeoPlugin\GeoPlugin;
use Geocoder\Provider\Provider;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeoPluginFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GeoPlugin::class, 'packageName' => 'geocoder-php/geo-plugin-provider'],
    ];

    /**
     * @param array{httplug_client: ?ClientInterface} $config
     */
    protected function getProvider(array $config): Provider
    {
        $httplug = $config['httplug_client'] ?: $this->httpClient ?? HttpClientDiscovery::find();

        return new GeoPlugin($httplug);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', [ClientInterface::class, 'null']);
    }
}
