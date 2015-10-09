<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class AddProvidersPass implements CompilerPassInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    /**
     * Get all providers based on their tag (`bazinga_geocoder.provider`) and
     * register them.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bazinga_geocoder.geocoder')) {
            return;
        }

        $providers = array();
        foreach ($container->findTaggedServiceIds('bazinga_geocoder.provider') as $providerId => $attributes) {
            $providers[] = new Reference($providerId);
        }

        $geocoderDefinition = $container->getDefinition('bazinga_geocoder.geocoder');
        $geocoderDefinition->addMethodCall('registerProviders', array($providers));

        if ($container->hasParameter('bazinga_geocoder.default_provider')) {
            $geocoderDefinition->addMethodCall(
                'using',
                array($container->getParameter('bazinga_geocoder.default_provider'))
            );
        }
    }
}
