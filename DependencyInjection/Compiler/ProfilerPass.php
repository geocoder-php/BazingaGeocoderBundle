<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\DependencyInjection\Compiler;

use Bazinga\GeocoderBundle\DataCollector\GeocoderDataCollector;
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

        foreach ($container->findTaggedServiceIds('bazinga_geocoder.profiling_plugin') as $providerId => $attributes) {
            $dataCollector->addMethodCall('addInstance', [new Reference($providerId)]);
        }
    }
}
