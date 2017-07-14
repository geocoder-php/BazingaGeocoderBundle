<?php

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler;

use Bazinga\Bundle\GeocoderBundle\DataCollector\GeocoderDataCollector;
use Bazinga\Bundle\GeocoderBundle\DataCollector\ProfilingProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add Profiling on all providers with that 'bazinga_geocoder.provider'.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ProfilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(GeocoderDataCollector::class)) {
            return;
        }

        $dataCollector = $container->getDefinition(GeocoderDataCollector::class);

        foreach ($container->findTaggedServiceIds('bazinga_geocoder.provider') as $providerId => $attributes) {
            $container->register($providerId.'.debug', ProfilingProvider::class)
                ->setDecoratedService($providerId)
                ->setArguments([
                    new Reference($providerId.'.debug.inner'),
                ]);
            $dataCollector->addMethodCall('addInstance', [new Reference($providerId)]);
        }
    }
}
