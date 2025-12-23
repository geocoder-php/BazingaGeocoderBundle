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

use Geocoder\Plugin\Plugin;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\Provider;

/**
 * This factory creates a PluginProvider.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PluginProviderFactory
{
    /**
     * @param Plugin[]                                          $plugins
     * @param callable(array):Provider|ProviderFactoryInterface $factory
     * @param array<mixed>                                      $config                config to the client factory
     * @param array{max_restarts?: int<0, max>}                 $pluginProviderOptions config forwarded to the PluginProvider
     */
    public static function createPluginProvider(array $plugins, callable|ProviderFactoryInterface $factory, array $config, array $pluginProviderOptions = []): PluginProvider
    {
        if ($factory instanceof ProviderFactoryInterface) {
            $client = $factory->createProvider($config);
        } else {
            $client = $factory($config);
        }

        return new PluginProvider($client, $plugins, $pluginProviderOptions);
    }
}
