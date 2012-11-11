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
    protected $container;

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

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
            $container->setParameter('bazinga_geocoder.geocoder.adapter.class', $config['adapter']['class']);
        }

        if (isset($config['providers']['free_geo_ip'])) {
            $this->addProvider('free_geo_ip');
        }

        if (isset($config['providers']['host_ip'])) {
            $this->addProvider('host_ip');
        }

        if (isset($config['providers']['bing_maps'])) {
            $bingMapsParams = $config['providers']['bing_maps'];

            $this->addProvider('bing_maps', array(
                $bingMapsParams['api_key'],
                $bingMapsParams['locale'],
            ));
        }

        if (isset($config['providers']['ip_info_db'])) {
            $ipInfoDbParams = $config['providers']['ip_info_db'];

            $this->addProvider('ip_info_db', array($ipInfoDbParams['api_key']));
        }

        if (isset($config['providers']['yahoo'])) {
            $yahooParams = $config['providers']['yahoo'];

            $this->addProvider('yahoo', array(
                $yahooParams['api_key'],
                $yahooParams['locale'],
            ));
        }

        if (isset($config['providers']['cloudmade'])) {
            $cloudMadeParams = $config['providers']['cloudmade'];

            $this->addProvider('cloudmade', array($cloudMadeParams['api_key']));
        }

        if (isset($config['providers']['google_maps'])) {
            $googleMapsParams = $config['providers']['google_maps'];

            $this->addProvider('google_maps', array($googleMapsParams['locale'], $googleMapsParams['region']));
        }

        if (isset($config['providers']['openstreetmaps'])) {
            $openstreetMapsParams = $config['providers']['openstreetmaps'];

            $this->addProvider('openstreetmaps', array($openstreetMapsParams['locale']));
        }

        if (isset($config['providers']['geoip'])) {
            $this->addProvider('geoip');
        }

        if (isset($config['providers']['mapquest'])) {
            $this->addProvider('mapquest');
        }

        if (isset($config['providers']['oiorest'])) {
            $this->addProvider('oiorest');
        }

        if (isset($config['providers']['geocoder_ca'])) {
            $this->addProvider('geocoder_ca');
        }

        if (isset($config['providers']['geocoder_us'])) {
            $this->addProvider('geocoder_us');
        }

        if (isset($config['providers']['ign_openls'])) {
            $ignOpenlsParams = $config['providers']['ign_opels'];

            $this->addProvider('ign_openls', array($ignOpenlsParams['api_key']));
        }

        if (isset($config['providers']['data_science_toolkit'])) {
            $this->addProvider('data_science_toolkit');
        }

        if (isset($config['providers']['yandex'])) {
            $yandexParams = $config['providers']['yandex'];

            $this->addProvider('yandex', array($yandexParams['locale'], $yandexParams['toponym']));
        }

        if (isset($config['provider']['cache'])) {
            $params = $config['provider']['cache'];

            $cache = new Reference($params['adapter']);
            $fallback = new Reference('bazinga_geocoder.geocoder.provider'.$params['provider']);

            $this->addProvider('cache', array($cache, $fallback));
        }
    }

    protected function addProvider($name, array $arguments = array())
    {
        $provider = new Definition(
            '%bazinga_geocoder.geocoder.provider.'.$name.'.class%',
            array_merge(
                array(new Reference('bazinga_geocoder.geocoder.adapter')),
                $arguments
            )
        );

        $provider
            ->setPublic(false)
            ->addTag('bazinga_geocoder.provider');

        $this->container->setDefinition('bazinga_geocoder.provider.'.$name, $provider);
    }
}
