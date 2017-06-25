<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection;

use Bazinga\Bundle\GeocoderBundle\ProviderFactory\ProviderFactoryInterface;
use Geocoder\Provider\Provider;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
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
        $configuration = $this->getConfiguration($configs, $container);
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $this->loadProviders($container, $config);

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

    private function loadProviders(ContainerBuilder $container, array $config)
    {
        foreach ($config['providers'] as $providerName => $providerConfig) {
            $factoryClass = $container->getDefinition($providerConfig['factory'])->getClass();
            if (!is_a($factoryClass, ProviderFactoryInterface::class)) {
                throw new \LogicException(sprintf('Provider factory "%s" must implement ProviderFactoryInterface', $providerConfig['factory']));
            }
            $factoryClass::validate($providerConfig['options'], $providerName);

            // See if any option has a service reference
            $arguments['options'] = $this->findReferences($providerConfig['options']);

            $def = $container->register('bazinga_geocoder.provider.'.$providerName, Provider::class);
            $def->setFactory([new Reference($arguments['factory']), 'createProvider'])
                ->addArgument($arguments['options']);

            $def->addTag('bazinga_geocoder.provider');
            foreach ($arguments['aliases'] as $alias) {
                $container->setAlias($alias, 'bazinga_geocoder.provider.'.$providerName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameter('kernel.debug'));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function findReferences(array $options)
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $options[$key] = $this->findReferences($value);
            } elseif (substr($key, -8) === '_service' || strpos($value, '@') === 0 || $key === 'service') {
                $options[$key] = new Reference(ltrim($value, '@'));
            }
        }

        return $options;
    }
}
