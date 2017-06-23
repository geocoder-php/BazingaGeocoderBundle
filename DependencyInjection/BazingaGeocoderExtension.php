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
 * William Durand <william.durand1@gmail.com>.
 */
class BazingaGeocoderExtension extends Extension
{
    protected $container;

    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['default_provider']) {
            $container->setParameter('bazinga_geocoder.default_provider', $config['default_provider']);
        }

        if (!empty($config['fake_ip']) && true === $config['fake_ip']['enabled']) {
            $definition = $container->getDefinition('bazinga_geocoder.event_listener.fake_request');
            $definition->replaceArgument(0, $config['fake_ip']['ip']);

            $tag = current($definition->getTag('kernel.event_listener'));
            $tag['priority'] = $config['fake_ip']['priority'];
            $tags = array('kernel.event_listener' => array($tag));
            $definition->setTags($tags);
        } else {
            $container->removeDefinition('bazinga_geocoder.event_listener.fake_request');
        }

        $container->setAlias('bazinga_geocoder.geocoder.adapter', $config['adapter']);


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
