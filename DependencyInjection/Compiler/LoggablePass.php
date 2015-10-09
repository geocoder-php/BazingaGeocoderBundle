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
 * @author Michal Dabrowski <dabrowski@brillante.pl>
 */
class LoggablePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bazinga_geocoder.geocoder')) {
            return;
        }

        $definition = $container->getDefinition('bazinga_geocoder.geocoder');
        $definition->setClass($container->getParameter('bazinga_geocoder.geocoder.loggable_class'));
        $definition->addMethodCall('setLogger', array(new Reference('bazinga_geocoder.logger')));
    }
}
