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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Make sure that the factory actually exists.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class FactoryValidatorPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private static array $factoryServiceIds = [];

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach (self::$factoryServiceIds as $id) {
            if (!$container->hasAlias($id) && !$container->hasDefinition($id)) {
                throw new ServiceNotFoundException(sprintf('Factory with ID "%s" could not be found', $id));
            }
        }
    }

    public static function addFactoryServiceId(string $factoryServiceId): void
    {
        self::$factoryServiceIds[] = $factoryServiceId;
    }
}
