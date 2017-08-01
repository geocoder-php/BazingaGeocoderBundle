<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\ProviderFactory;

use Geocoder\Provider\GeoPlugin\GeoPlugin;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeoPluginFactory extends AbstractFactory
{
    protected static $dependencies = [
        ['requiredClass' => GeoPlugin::class, 'packageName' => 'geocoder-php/geo-plugin-provider'],
    ];

    protected function getProvider(array $config)
    {
        $httplug = $config['httplug_client'] ?: HttpClientDiscovery::find();

        return new GeoPlugin($httplug);
    }

    protected static function configureOptionResolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'httplug_client' => null,
        ]);

        $resolver->setAllowedTypes('httplug_client', [HttpClient::class, 'null']);
    }
}
