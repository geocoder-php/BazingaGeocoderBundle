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

/**
 * This factory creates a PluginProvider.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PluginProviderFactory
{
    /**
     * @param Plugin[]                          $plugins
     * @param ProviderFactoryInterface|callable $factory
     * @param array                             $config                config to the client factory
     * @param array                             $pluginProviderOptions config forwarded to the PluginProvider
     *
     * @return PluginProvider
     */
    public static function createPluginProvider(array $plugins, $factory, array $config, array $pluginProviderOptions = []): PluginProvider
    {
        if ($factory instanceof ProviderFactoryInterface) {
            $client = $factory->createProvider($config);
        } elseif (is_callable($factory)) {
            $client = $factory($config);
        } else {
            throw new \RuntimeException(sprintf('Second argument to PluginProviderFactory::createPluginProvider must be a "%s" or a callable.', ProviderFactoryInterface::class));
        }

        return new PluginProvider($client, $plugins, $pluginProviderOptions);
    }
}
