<?php

/**
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */
namespace Bazinga\Bundle\GeocoderBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\AddProvidersPass;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\AddDumperPass;
use Bazinga\Bundle\GeocoderBundle\DependencyInjection\Compiler\LoggablePass;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class BazingaGeocoderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new AddDumperPass());
        $container->addCompilerPass(new LoggablePass());
    }
}
