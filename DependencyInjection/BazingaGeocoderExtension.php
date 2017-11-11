<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\DependencyInjection;

use Bazinga\GeocoderBundle\DataCollector\GeocoderDataCollector;
use Bazinga\GeocoderBundle\Plugin\FakeIpPlugin;
use Bazinga\GeocoderBundle\Plugin\ProfilingPlugin;
use Bazinga\GeocoderBundle\ProviderFactory\PluginProviderFactory;
use Bazinga\GeocoderBundle\ProviderFactory\ProviderFactoryInterface;
use Geocoder\Plugin\Plugin\CachePlugin;
use Geocoder\Plugin\Plugin\LimitPlugin;
use Geocoder\Plugin\Plugin\LocalePlugin;
use Geocoder\Plugin\Plugin\LoggerPlugin;
use Geocoder\Plugin\PluginProvider;
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

        if ($config['fake_ip']['enabled']) {
            $definition = $container->getDefinition(FakeIpPlugin::class);
            $definition->replaceArgument(1, $config['fake_ip']['ip']);
        } else {
            $container->removeDefinition(FakeIpPlugin::class);
        }

        $this->loadProviders($container, $config);
    }

    private function loadProviders(ContainerBuilder $container, array $config)
    {
        foreach ($config['providers'] as $providerName => $providerConfig) {
            $factoryService = $container->getDefinition($providerConfig['factory']);
            $factoryClass = $factoryService->getClass() ?: $providerConfig['factory'];
            if (!$this->implementsPoviderFactory($factoryClass)) {
                throw new \LogicException(sprintf('Provider factory "%s" must implement ProviderFactoryInterface', $providerConfig['factory']));
            }
            $factoryClass::validate($providerConfig['options'], $providerName);

            // See if any option has a service reference
            $providerConfig['options'] = $this->findReferences($providerConfig['options']);

            $serviceId = 'bazinga_geocoder.provider.'.$providerName;
            $plugins = $this->configureProviderPlugins($container, $providerConfig, $serviceId);

            $def = $container->register($serviceId, PluginProvider::class)
                ->setFactory([PluginProviderFactory::class, 'createPluginProvider'])
                ->addArgument($plugins)
                ->addArgument(new Reference($providerConfig['factory']))
                ->addArgument($providerConfig['options']);

            $def->addTag('bazinga_geocoder.provider');
            foreach ($providerConfig['aliases'] as $alias) {
                $container->setAlias($alias, $serviceId);
            }
        }
    }

    /**
     * Configure plugins for a client.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     * @param string           $providerServiceId
     *
     * @return array
     */
    public function configureProviderPlugins(ContainerBuilder $container, array $config, string $providerServiceId): array
    {
        $plugins = [];
        foreach ($config['plugins'] as $plugin) {
            $plugins[] = $plugin['id'];
        }

        if (isset($config['cache']) || isset($config['cache_lifetime'])) {
            if (null === $cacheServiceId = $config['cache']) {
                if (!$container->has('app.cache')) {
                    throw new \LogicException('You need to specify a service for cache.');
                }
                $cacheServiceId = 'app.cache';
            }
            $plugins[] = $providerServiceId.'.cache';
            $container->register($providerServiceId.'.cache', CachePlugin::class)
                ->setPublic(false)
                ->setArguments([new Reference($cacheServiceId), (int) $config['cache_lifetime']]);
        }

        if (isset($config['limit'])) {
            $plugins[] = $providerServiceId.'.limit';
            $container->register($providerServiceId.'.limit', LimitPlugin::class)
                ->setPublic(false)
                ->setArguments([(int) $config['limit']]);
        }

        if (isset($config['locale'])) {
            $plugins[] = $providerServiceId.'.locale';
            $container->register($providerServiceId.'.locale', LocalePlugin::class)
                ->setPublic(false)
                ->setArguments([$config['locale']]);
        }

        if (isset($config['logger'])) {
            $plugins[] = $providerServiceId.'.logger';
            $container->register($providerServiceId.'.logger', LoggerPlugin::class)
                ->setPublic(false)
                ->setArguments([new Reference($config['logger'])]);
        }

        if ($container->has(FakeIpPlugin::class)) {
            $plugins[] = FakeIpPlugin::class;
        }

        if ($container->has(GeocoderDataCollector::class)) {
            $plugins[] = $providerServiceId.'.profiler';
            $container->register($providerServiceId.'.profiler', ProfilingPlugin::class)
                ->setPublic(false)
                ->setArguments([substr($providerServiceId, strlen('bazinga_geocoder.provider.'))])
                ->addTag('bazinga_geocoder.profiling_plugin');
        }

        return array_map(function (string $id) {
            return new Reference($id);
        }, $plugins);
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
    private function findReferences(array $options): array
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $options[$key] = $this->findReferences($value);
            } elseif ('_service' === substr((string) $key, -8) || 0 === strpos((string) $value, '@') || 'service' === $key) {
                $options[$key] = new Reference(ltrim($value, '@'));
            }
        }

        return $options;
    }

    /**
     * @param mixed $factoryClass
     *
     * @return bool
     */
    private function implementsPoviderFactory($factoryClass): bool
    {
        if (false === $interfaces = class_implements($factoryClass)) {
            return false;
        }

        return in_array(ProviderFactoryInterface::class, $interfaces);
    }
}
