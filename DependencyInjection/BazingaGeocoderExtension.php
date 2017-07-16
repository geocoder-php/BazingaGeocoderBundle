<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection;

use Bazinga\Bundle\GeocoderBundle\ProviderFactory\ProviderFactoryInterface;
use Geocoder\Provider\Cache\ProviderCache;
use Geocoder\Provider\Provider;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * William Durand <william.durand1@gmail.com>.
 */
class BazingaGeocoderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = $this->getConfiguration($configs, $container);
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (true === $config['profiling']['enabled']) {
            $loader->load('profiling.yml');
        }
        $this->loadProviders($container, $config);

        if ($config['fake_ip']['enabled']) {
            $definition = $container->getDefinition('bazinga_geocoder.event_listener.fake_request');
            $definition->replaceArgument(0, $config['fake_ip']['ip']);
        } else {
            $container->removeDefinition('bazinga_geocoder.event_listener.fake_request');
        }
    }

    private function loadProviders(ContainerBuilder $container, array $config)
    {
        foreach ($config['providers'] as $providerName => $providerConfig) {
            $factoryService = $container->getDefinition($providerConfig['factory']);
            $factoryClass = $factoryService->getClass() ?: $providerConfig['factory'];
            if (!(is_a($factoryClass, ProviderFactoryInterface::class))) {
                //throw new \LogicException(sprintf('Provider factory "%s" must implement ProviderFactoryInterface', $providerConfig['factory']));
            }
            $factoryClass::validate($providerConfig['options'], $providerName);

            // See if any option has a service reference
            $providerConfig['options'] = $this->findReferences($providerConfig['options']);

            $serviceId = 'bazinga_geocoder.provider.'.$providerName;
            $def = $container->register($serviceId, Provider::class);
            $def->setFactory([new Reference($providerConfig['factory']), 'createProvider'])
                ->addArgument($providerConfig['options']);

            $def->addTag('bazinga_geocoder.provider');
            foreach ($providerConfig['aliases'] as $alias) {
                $container->setAlias($alias, $serviceId);
            }

            $this->configureCache($container, $serviceId, $providerConfig);
        }
    }

    /**
     * Add cache to a provider if needed.
     *
     * @param ContainerBuilder $
     * @param string $serviceId
     * @param array  $providerConfig
     */
    private function configureCache(ContainerBuilder $container, string $serviceId, array $providerConfig)
    {
        if (null === $providerConfig['cache'] && null === $providerConfig['cache_lifetime']) {
            return;
        }

        if (!class_exists(ProviderCache::class)) {
            throw new \LogicException('You must install "geocoder-php/cache-provider" to use cache.');
        }

        if (null === $cacheServiceId = $providerConfig['cache']) {
            if (!$container->has('app.cache')) {
                throw new \LogicException('You need to specify a service for cache.');
            }
            $cacheServiceId = 'app.cache';
        }

        $container->register($serviceId.'.cache', ProviderCache::class)
            ->setDecoratedService($serviceId)
            ->setArguments([
                new Reference($serviceId.'.cache.inner'),
                new Reference($cacheServiceId),
                $providerConfig['cache_lifetime'],
            ]);
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
