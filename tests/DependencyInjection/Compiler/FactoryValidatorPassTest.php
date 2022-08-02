<?php

declare(strict_types=1);

/*
 * This file is part of the BazingaGeocoderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\GeocoderBundle\Tests\DependencyInjection\Compiler;

use Bazinga\GeocoderBundle\DependencyInjection\Compiler\FactoryValidatorPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class FactoryValidatorPassTest extends TestCase
{
    private FactoryValidatorPass $compilerPass;
    private string $factoryId;

    protected function setUp(): void
    {
        $this->compilerPass = new FactoryValidatorPass();
        $this->factoryId = 'dummy_factory_id';
        $this->compilerPass::addFactoryServiceId($this->factoryId);
    }

    protected function tearDown(): void
    {
        $reflection = new \ReflectionObject($this->compilerPass);
        $prop = $reflection->getProperty('factoryServiceIds');
        $prop->setAccessible(true);
        $prop->setValue([]);
    }

    public function testProcessThrows(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage("Factory with ID \"$this->factoryId\" could not be found");

        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(false);

        $this->compilerPass->process($container);
    }

    public function testProcessDoesntThrowIfAliasExists(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(true);
        $container->expects($this->never())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(false);

        $this->compilerPass->process($container);
    }

    public function testProcessDoesntThrowIfDefinitionExists(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasAlias')
            ->with($this->factoryId)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with($this->factoryId)
            ->willReturn(true);

        $this->compilerPass->process($container);
    }
}
