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
     * Get all providers based on their tag ('geocoder.provider') and register them.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container    The container.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bazinga_geocoder.geocoder')) {
            return;
        }

        $this->container = $container;

        $array = array();
        foreach ($this->container->findTaggedServiceIds('bazinga_geocoder.provider') as $providerId => $attributes) {
            $array[] = new Reference($providerId);
        }

        $this->container
            ->getDefinition('bazinga_geocoder.geocoder')
            ->addMethodCall('registerProviders', array($array))
            ;
    }
}
