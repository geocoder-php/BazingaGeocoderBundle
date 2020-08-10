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
use Bazinga\GeocoderBundle\DependencyInjection\Compiler\FactoryValidatorPass;
use Bazinga\GeocoderBundle\Plugin\FakeIpPlugin;
use Bazinga\GeocoderBundle\Plugin\ProfilingPlugin;
use Bazinga\GeocoderBundle\ProviderFactory\PluginProviderFactory;
use Bazinga\GeocoderBundle\ProviderFactory\ProviderFactoryInterface;
use Faker\Generator;
use Geocoder\Dumper\Dumper;
use Geocoder\Plugin\Plugin\CachePlugin;
use Geocoder\Plugin\Plugin\LimitPlugin;
use Geocoder\Plugin\Plugin\LocalePlugin;
use Geocoder\Plugin\Plugin\LoggerPlugin;
use Geocoder\Plugin\PluginProvider;
use Geocoder\Provider\Provider;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author William Durand <william.durand1@gmail.com>.
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
            $definition->replaceArgument(0, $config['fake_ip']['local_ip']);
            $definition->replaceArgument(1, $config['fake_ip']['ip']);
            $definition->replaceArgument(2, $config['fake_ip']['use_faker']);

            if ($config['fake_ip']['use_faker'] && !class_exists(Generator::class)) {
                throw new \LogicException('To enable this option, you must install fzaninotto/faker package.');
            }
        } else {
            $container->removeDefinition(FakeIpPlugin::class);
        }

        $this->loadProviders($container, $config);

        $container->registerForAutoconfiguration(Dumper::class)
            ->addTag('bazinga_geocoder.dumper');
    }

    private function loadProviders(ContainerBuilder $container, array $config)
    {
        foreach ($config['providers'] as $providerName => $providerConfig) {
            try {
                $factoryService = $container->getDefinition($providerConfig['factory']);
                $factoryClass = $factoryService->getClass() ?: $providerConfig['factory'];
                if (!$this->implementsProviderFactory($factoryClass)) {
                    throw new \LogicException(sprintf('Provider factory "%s" must implement ProviderFactoryInterface', $providerConfig['factory']));
                }
                // See if any option has a service reference
                $providerConfig['options'] = $this->findReferences($providerConfig['options']);
                $factoryClass::validate($providerConfig['options'], $providerName);
            } catch (ServiceNotFoundException $e) {
                // Assert: We are using a custom factory. If invalid config, it will be caught in FactoryValidatorPass
                $providerConfig['options'] = $this->findReferences($providerConfig['options']);
                FactoryValidatorPass::addFactoryServiceId($providerConfig['factory']);
            }

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

            if (Kernel::VERSION_ID > 40200) {
                $container->registerAliasForArgument($serviceId, Provider::class, "{$providerName}Geocoder");
            }
        }
    }

    /**
     * Configure plugins for a client.
     */
    public function configureProviderPlugins(ContainerBuilder $container, array $config, string $providerServiceId): array
    {
        $plugins = [];
        foreach ($config['plugins'] as $plugin) {
            if ($plugin['reference']['enabled']) {
                $plugins[] = $plugin['reference']['id'];
            }
        }

        if (isset($config['cache']) || isset($config['cache_lifetime']) || isset($config['cache_precision'])) {
            $cacheLifetime = isset($config['cache_lifetime']) ? (int) $config['cache_lifetime'] : null;

            if (null === $cacheServiceId = $config['cache']) {
                if (!$container->has('app.cache')) {
                    throw new \LogicException('You need to specify a service for cache.');
                }
                $cacheServiceId = 'app.cache';
            }
            $plugins[] = $providerServiceId.'.cache';
            $container->register($providerServiceId.'.cache', CachePlugin::class)
                ->setPublic(false)
                ->setArguments([new Reference($cacheServiceId), $cacheLifetime, $config['cache_precision']]);
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
     */
    private function implementsProviderFactory($factoryClass): bool
    {
        if (false === $interfaces = class_implements($factoryClass)) {
            return false;
        }

        return in_array(ProviderFactoryInterface::class, $interfaces, true);
    }
}
