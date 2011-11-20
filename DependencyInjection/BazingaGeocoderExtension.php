<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * William Durand <william.durand1@gmail.com>
 */
class BazingaGeocoderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor      = new Processor();
        $configuration  = new Configuration();
        $config         = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['fake_ip'])) {
            $container
                ->getDefinition('bazinga_geocoder.event_listener.fake_request')
                ->replaceArgument(0, $config['fake_ip'])
                ;
        }

        if (isset($config['adapter']['class']) && !empty($config['adapter']['class'])) {
            $container->setParameter('bazinga_geocoder.geocoder.adapter.class', $config['adapter']);
        }

        if (isset($config['providers']['bing_maps'])) {
            $bingMapsParams = $config['providers']['bing_maps'];

            $container
                ->getDefinition('bazinga_geocoder.provider.bing_maps')
                ->replaceArgument(1, $bingMapsParams['api_key'])
                ->replaceArgument(2, $bingMapsParams['locale'])
                ;
        }

        if (isset($config['providers']['ip_info_db'])) {
            $bingMapsParams = $config['providers']['bing_maps'];

            $container
                ->getDefinition('bazinga_geocoder.provider.ip_info_db')
                ->replaceArgument(1, $bingMapsParams['api_key'])
                ;
        }

        if (isset($config['providers']['yahoo'])) {
            $bingMapsParams = $config['providers']['yahoo'];

            $container
                ->getDefinition('bazinga_geocoder.provider.bing_maps')
                ->replaceArgument(1, $bingMapsParams['api_key'])
                ->replaceArgument(2, $bingMapsParams['locale'])
                ;
        }

        if (isset($config['providers']['google_maps'])) {
            $bingMapsParams = $config['providers']['google_maps'];

            $container
                ->getDefinition('bazinga_geocoder.provider.bing_maps')
                ->replaceArgument(1, $bingMapsParams['api_key'])
                ;
        }
    }
}
